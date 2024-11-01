<?php

/*
  Easy Pie Site Spy Plugin
  Copyright (C) 2015, Synthetic Thought LLC
  website: easypiewp.com contact: bob@easypiewp.com

  Easy Pie Site Spy Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once(dirname(__FILE__) . '/Utilities/class-ezp-ibc-u.php');

require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-global-entity.php');
require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-contact-entity.php');
require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-event-entity.php');


if (!class_exists('EZP_IBC_Storage_Manager'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Storage_Manager
    {

        public function run_maintenance_cycle()
        {
            $global = EZP_IBC_Global_Entity::get_instance();

            $this->clean_inactive_prospects($global);
            $this->clean_url_history_by_time($global);
            //$this->clean_url_history_by_count($global);

     //       $sizes = EZP_IBC_U::get_database_sizes();

      //      $this->check_wordpress_db_size($global, $sizes);
      //      $this->check_ibc_table_size($global, $sizes);
        }      
        
        public static function get_alert_text()
        {
            $alert_message = '';
            $global = EZP_IBC_Global_Entity::get_instance();
            $error_present = false;
            $policy_enforced = false;
                    
            /* @var $global EZP_IBC_Global_Entity */

            $sizes = EZP_IBC_U::get_database_sizes();
            
            if($global->storage_alerts_wordpress_db_size_in_MB > 0)                
            {
                $policy_enforced = true;
                if($sizes[0] > $global->storage_alerts_wordpress_db_size_in_MB)
                {
                    $alert_message .= '<p>' . __('WORDPRESS DATABASE SIZE is') . ' ' . $sizes[0] . ' MB!</p>';
                    $error_present = true;
                }
            }
            
            if($global->storage_alerts_ibc_table_size_in_MB > 0)
            {
                $policy_enforced = true;
                if($sizes[1] > $global->storage_alerts_ibc_table_size_in_MB)
                {
                    $alert_message .= '<p>' . __('SITE SPY TABLE SIZE IS') . ' ' . $sizes[1] . ' MB!</p>';
                    $error_present = true;
                }
            }
            
            if($policy_enforced)
            {
                if($error_present)
                {
                    $class = 'error';
                    $url = menu_page_url(EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG, false);
                    $url = EZP_IBC_U::append_query_value($url, 'tab', 'storage');
                                        
                    $alert_message .= "<p><a href='$url'>" . __('Storage Settings') . '</a></p>';                        
                    $alert_message = "<div style='margin-top:12px' class='$class'>$alert_message</div>";
                }
                else
                {
//                    $class = 'updated';
//                    $alert_message = '<p>' . __('Database and table size within configured limits.') . '</p>';
                    $alert_message = '';
                    
                }
                
                //$alert_message = "<div style='margin-top:12px' class='$class'>$alert_message</div>";
            }
            
            return $alert_message;
        }

        private function clean_inactive_prospects($global)
        {
            /* @var $global EZP_IBC_Global_Entity */
            global $wpdb;

            $max_age_in_secs = (int) $global->storage_pp_max_inactive_prospect_age_in_days * 24 * 3600;

            $cutoff_date = time() - $max_age_in_secs;
            $formatted_cutoff_date = date("Y-m-d H:i:s", $cutoff_date);

            EZP_IBC_U::debug("Purging prospects who have been inactive since $formatted_cutoff_date");

            $event_table_name = $wpdb->prefix . EZP_IBC_Event_Entity::$TABLE_NAME;

            $query_string = "SELECT a.contact_id from ";
            $query_string .= "(SELECT max(timestamp) AS maxtime, contact_id FROM $event_table_name GROUP BY contact_id) as a ";
            $query_string .= "WHERE maxtime < '$formatted_cutoff_date'";

            $contact_ids = $wpdb->get_col($query_string);

            if (count($contact_ids) > 0)
            {
                EZP_IBC_U::debug_object("purging prospects", $contact_ids);
                $contacts = EZP_IBC_Contact_Entity::get_all_by_contact_ids($contact_ids, 'stage = ' . EZP_IBC_Contact_Stages::Prospect);

                foreach ($contacts as $contact)
                {
                    EZP_IBC_U::debug("Purging prospect $contact->id with creation time $contact->creation_timestamp");
                    /* @var $contact EZP_IBC_Contact_Entity */
                    $contact->delete();
                }
            }
        }

        private function clean_url_history_by_time($global)
        {
            $max_age_in_secs = (int) $global->storage_pp_page_history_retention_in_days * 24 * 3600;

            $cutoff_date = time() - $max_age_in_secs;
            $formatted_cutoff_date = date("Y-m-d H:i:s", $cutoff_date);

            EZP_IBC_U::debug("Cleaning URL history that occurred before $formatted_cutoff_date");

            $type = EZP_IBC_Event_Types::Page_Load;

            EZP_IBC_Event_Entity::delete_where(EZP_IBC_Event_Entity::$TABLE_NAME, "(type = $type) AND (timestamp < '$formatted_cutoff_date')");
        }

        private function clean_url_history_by_count($global)
        {
            global $wpdb;

            EZP_IBC_U::debug("cleaning url history by count");

            /* @var $global EZP_IBC_Global_Entity */

            $event_table_name = $wpdb->prefix . EZP_IBC_Event_Entity::$TABLE_NAME;

            /* 1 */
            $query_string = 'SELECT a.contact_id FROM ';
            $query_string .= "(SELECT contact_id, count(id) AS num_events FROM $event_table_name GROUP BY contact_id) as a ";
            $query_string .= "where a.num_events > $global->storage_pp_max_pages_per_visitor";

            $contact_ids = $wpdb->get_col($query_string);

            ///* 2 */

            if (count($contact_ids > 0))
            {
                foreach ($contact_ids as $contact_id)
                {
                    $query_string_2 = "DELETE FROM $event_table_name WHERE id in ";
                    $query_string_2 .= "(SELECT id as event_id FROM $event_table_name WHERE contact_id = $contact_id ORDER BY timestamp DESC LIMIT $global->storage_pp_max_pages_per_visitor, 18446744073709551615);";

                    $wpdb->query($query_string_2);
                }
            }
        }

//        private function check_wordpress_db_size($global, $sizes)
//        {
//            /* @var $global EZP_IBC_Global_Entity */

//            if (!empty($global->notify_email))
//            {
//                if (($global->storage_alerts_wordpress_db_size_in_MB != -1 ) && $sizes[0] > $global->storage_alerts_wordpress_db_size_in_MB)
//                {
//                    $display_name = $this->get_display_name();
//
//                    $info = "Contact $display_name ($this->id) transitioned to from stage $from_stage_string to $to_stage_string.";
//
//                    $subject = "WordPress database has exceeded maximum threshold of $global->storage_alerts_wordpress_db_size_in_MB MB. The database is currently $sizes[0] MB.";
//                    $message = __('ALERT! WordPress Database is now') . ' ' . "$sizes[0] MB";
//
//                    if (wp_mail($global->notify_email, $subject, $message))
//                    {
//                        EZP_IBC_U::debug($info . 'successfully sent');
//                    } else
//                    {
//                        EZP_IBC_U::debug($info . 'failed to be sent');
//                    }
//                }
//            } else
//            {
//                EZP_IBC_U::debug("Tried to send email regarding WordPress database exceeding $global->storage_alerts_wordpress_db_size_in_MB MB ($sizes[0] MB) but notification email address not configured.");
//            }
//        }

//        private function check_ibc_table_size($global, $sizes)
//        {
//            /* @var $global EZP_IBC_Global_Entity */

//            if (!empty($global->notify_email))
//            {
//                if (($global->storage_alerts_ibc_table_size_in_MB != -1) && $sizes[1] > $global->storage_alerts_ibc_table_size_in_MB)
//                {
//                    $display_name = $this->get_display_name();
//
//                    $info = "Contact $display_name ($this->id) transitioned to from stage $from_stage_string to $to_stage_string.";
//
//                    $subject = "Easy Pie IBC tracking tables exceeded maximum threshold of $global->storage_alerts_ibc_table_size_in_MB MB. The tables are currently $sizes[1] MB.";
//                    $message = __('ALERT! Easy Pie IBC tracking tables are now') . ' ' . "$sizes[1] MB";
//
//                    if (wp_mail($global->notify_email, $subject, $message))
//                    {
//                        EZP_IBC_U::debug($info . 'successfully sent');
//                    } else
//                    {
//                        EZP_IBC_U::debug($info . 'failed to be sent');
//                    }
//                }
//            } else
//            {
//                EZP_IBC_U::debug("Tried to send email regarding Easy Pie IBC tables exceeding $global->storage_alerts_ibc_table_size_in_MB MB ($sizes[1] MB) but notification email address not configured.");
//            }
 //       }
    }
}
?>