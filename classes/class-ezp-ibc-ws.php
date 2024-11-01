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

/**
 * @author Bob Riley <bob@easypiewp.com>
 * @copyright 2015 Synthetic Thought LLC
 */
require_once(dirname(__FILE__) . '/Utilities/class-ezp-ibc-u.php');
require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-page-trigger-entity.php');
require_once(dirname(__FILE__) . '/Entities/class-ezp-ibc-list-entity.php');
require_once(dirname(__FILE__) . '/class-ezp-ibc-report-helper.php');

class EZP_IBC_WS
{

    public function init()
    {
        $this->add_class_action('wp_ajax_nopriv_EZP_IBC_track_event', 'ws_track_event');
        $this->add_class_action('wp_ajax_EZP_IBC_track_event', 'ws_track_event');
        $this->add_class_action('wp_ajax_EZP_IBC_get_contacts', 'ws_get_contacts');
        $this->add_class_action('wp_ajax_EZP_IBC_export_contacts', 'ws_export_contacts');
        $this->add_class_action('wp_ajax_EZP_IBC_add_contacts_to_list', 'ws_add_contacts_to_list');
        $this->add_class_action('wp_ajax_EZP_IBC_export_list', 'ws_export_list');
        $this->add_class_action('wp_ajax_EZP_IBC_get_debug_file', 'ws_get_debug_file');
        $this->add_class_action('wp_ajax_EZP_IBC_get_recent_activity', 'ws_get_recent_activity');
    }

    function ws_track_event()
    {       
        $request = stripslashes_deep($_REQUEST);

        $type = $request['type'];
        $parameter = $request['parameter'];
        $parameter2 = $request['parameter2'];
        $parameter3 = $request['parameter3'];
        $data = $request['data'] == null ? '' : $request['data'];

        EZP_IBC_U::debug("ajax event: $type $parameter");
//       EZP_IBC_U::debug_object("ajax parameter2", $parameter2);

        $contact = EZP_IBC_Contact_Entity::prep_contact_for_event($_SERVER, $_COOKIE, $type, $data, $parameter, $parameter2, $parameter3);

        if ($contact->wpid != -1)
        {
            $global = EZP_IBC_Global_Entity::get_instance();

         //   if ($global->track_logged_in_users == 0)
            {
          //      return;
            }
        }

        if ($contact != null)
        {
            EZP_IBC_U::debug("Contact not null so saving event");
            
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $event = new EZP_IBC_Event_Entity();
            $event->type = $type;
            $event->contact_id = $contact->id;
            $event->ip_address = $ip_address;
            $event->parameter = self::scrub_encoded_string($parameter);
            $event->parameter2 = self::scrub_encoded_string($parameter2);
            $event->parameter3 = self::scrub_encoded_string($parameter3);
            $event->data = self::scrub_encoded_string(json_encode($data));
                    
            $event->save();

            $contact->update_last_activity_timestamp($ip_address);

     //       $contact->add_to_event_count($type, $user_event_type->id);
            
            $contact->score += EZP_IBC_Event_Entity::get_worth($type, $event->parameter, $event->parameter2, $event->parameter3);
            
            $contact->save();

            if ($type == EZP_IBC_Event_Types::Page_Load)
            {
                EZP_IBC_Page_Trigger_Entity::process_url($contact->id, $parameter, $ip_address);
            }

            $display_name = $contact->get_display_name();

            EZP_IBC_U::debug("Saved new event for $display_name($contact->id)");
        }
        else
        {
            EZP_IBC_U::debug("Not recording event $type because couldnt create/lookup contact");
        }

        //    if (isset($request['_wpnonce'])) {
        //        $_wpnonce = $request['_wpnonce'];
        //    if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers')) {
//                    if (isset($request['contact_id'])) {
//
//                        $contact_id = $request['contact_id'];
//
//                        EZP_Contact_Entity::delete_by_id($contact_id);
//                    } else {
//                        EZP_IBC_U::debug("ws_purge_contact: contact id not set");
//                    }
        //   } else {
        //         EZP_IBC_U::debug("ws_purge_contact: Security violation. Nonce doesn't properly match!");
        //     }
        //       } else {
        //             EZP_IBC_U::debug("ws_purge_contact: Security violation. Nonce doesn't exist!");
        //        }
    }

    static function scrub_encoded_string($s)
    {
        $s = str_replace('<<<', '[', $s);
        $s = str_replace('>>>', ']', $s);

        $s = trim($s, '"');
        
        return $s;
    }

    function ws_get_recent_activity()
    {
        //rsr todo
//        if (isset($_REQUEST['_wpnonce'])) {
//
//                $_wpnonce = $_REQUEST['_wpnonce'];

        
        $recent_event_count_minutes = 30;
        $max_events = 200;
        $max_contacts = 5;
        $time_threshold = time() - 5 * 60;
        $time_threshold = EZP_IBC_U::ticks_to_standard_formatted($time_threshold);
               
        $request = stripslashes_deep($_REQUEST);       
        
        $activity_stats = new stdClass();
        
        $activity_stats->visitor_count = 0;
        $activity_stats->latest_events = array();
        //rsr todo$activity_stats->recent_event_counts = EZP_IBC_Event_Entity::get_recent_event_counts($recent_event_count_minutes);
                                 

                                              
        /* ---- Events ---- */
     //   if($index == -1)
     //   {
            // First request so just pull in the last x events
            $events = EZP_IBC_Event_Entity::get_limited_events("timestamp > '$time_threshold'", 'timestamp DESC', $max_events);
   //     }
    //    else
  //      {
  //          // Pull in any new things
   //         $events = EZP_IBC_Event_Entity::get_all_where("id > $index", 'id DESC');
   //     }
         
        
        $last_events = array();
        $event_datum = array();
        foreach ($events as $event)
        {
            /* @var $event EZP_IBC_Event_Entity */
          //  EZP_IBC_U::debug("Trying to retrieve contact # $event->contact_id");
            $contact = EZP_IBC_Contact_Entity::get_by_id($event->contact_id);

            if($contact == null)
            {
                EZP_IBC_U::debug("contact is null!!");                
            }
            
            $timestamp = $event->timestamp;
            $contact_name = $contact->get_display_name();
            $event_type_string = EZP_IBC_Event_Entity::get_type_string($event->type);
            $event_virtual_name = $event->get_virtual_name();
            
            $event_datum['index'] = $event->id;
            
            //$adjusted_time = EZP_IBC_U::get_wp_formatted_from_gmt_formatted_time($timestamp, false, true);
            $adjusted_time = EZP_IBC_U::get_formatted_local_time_from_gmt($timestamp, 'g:i:s a');
            
            $color = $event->get_color();
            
            $event_url = EZP_IBC_U::append_query_value('', 'page', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG);
            $event_url = EZP_IBC_U::append_query_value($event_url, 'tab', 'events');
            $event_url = EZP_IBC_U::append_query_value($event_url, 'contact_id', $event->contact_id);
            
            $contact_url = EZP_IBC_U::append_query_value('', 'page', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG);
            $contact_url = EZP_IBC_U::append_query_value($contact_url, 'tab', 'edit');
            $contact_url = EZP_IBC_U::append_query_value($contact_url, 'contact_id', $event->contact_id);
            
            $defailt = "<div>" . $event->get_virtual_name() . '</div>';
            
            $details = $event->get_display_details();
            
           // $details='bob';
            if($details == '')
            {
                $details = $event->get_virtual_name();
            }
            
//            $name = $event->get_virtual_name();
            
//easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.displayDetails            
            //$event_datum['text'] = sprintf(__('<span style="color:%5$s"><strong>%1s</strong> <a href="%6$s" target="_blank">%3$s</a> (%4$s) by <a target="_blank" href="%7$s">%2$s</a></span>'), $adjusted_time, $contact_name, $event_type_string, $event_virtual_name, $color, $event_url, $contact_url); 
            $event_datum['text'] = sprintf(__('<span style="color:%5$s"><strong>%1s</strong> <a target="_blank" href="%6$s">%3$s</a> (%4$s) by <a href="%7$s" target="_blank" >%2$s</a></span>'), $adjusted_time, $contact_name, $event_type_string, $event_virtual_name, $color, $event_url, $contact_url); 
                       
            array_push($activity_stats->latest_events, $event_datum);
            
            if(!array_key_exists($event->contact_id, $last_events))
            {
                $last_events[$event->contact_id] = $event;
            }
        }
        
        
        /* Visitors */
        $active_contacts = EZP_IBC_Contact_Entity::get_all_where("last_activity_timestamp > '$time_threshold'", 'last_activity_timestamp DESC');
        
        $activity_stats->visitor_count = count($active_contacts);
        
        $activity_stats->active_contacts = array_slice($active_contacts, 0, $max_contacts);
        
        foreach($activity_stats->active_contacts as $active_contact)
        {
            /* @var $active_contact EZP_IBC_Contact_Entity */
            
            $active_contact->last_hostname = $active_contact->get_last_hostname();
            $active_contact->last_hostname = "<a target='_blank' href='http://www.ip-adress.com/ip_tracer/$active_contact->last_hostname'>$active_contact->last_hostname</a>"; 
            
            $edit_url = EZP_IBC_U::append_query_value('', 'page', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG);
            $edit_url = EZP_IBC_U::append_query_value($edit_url, 'contact_id', $active_contact->id);
            $edit_url = EZP_IBC_U::append_query_value($edit_url, 'tab', 'edit');
            
            //EZP_IBC_U::debug("edit url $edit_url");
            //$edit_url = EZP_IBC_U::append_query_value($edit_url, 'tab', 'edit');
            //$edit_url = EZP_IBC_U::append_query_value($edit_url, 'contact_id', $active_contact->id);
            
            $active_contact->display_name = "<a href='$edit_url'>" . $active_contact->get_display_name(). '</a>';
            
            $active_contact->time_delta_in_sec = time() - strtotime($active_contact->last_activity_timestamp);
            
            $minutes = intval(date('i', $active_contact->time_delta_in_sec));
            
            $active_contact->time_delta_in_sec = gmdate(":s", $active_contact->time_delta_in_sec);
            $active_contact->time_delta_in_sec = $minutes . $active_contact->time_delta_in_sec;                        
                    
            if(array_key_exists($active_contact->id, $last_events))
            {
                /* @var $event EZP_IBC_Event_Entity */
                $event = $last_events[$active_contact->id];
                
                
                $active_contact->last_event_description = sprintf(__('%1$s (%2$s)'), EZP_IBC_Event_Entity::get_type_string($event->type), $event->get_virtual_name());
            }
            else
            {
                $active_contact->last_event_description = __('Unknown');
            }
        }   
        
        

        $encoded = json_encode($activity_stats);

        echo $encoded;
        die();
    }

    function ws_get_contacts()
    {
        //rsr todo
//        if (isset($_REQUEST['_wpnonce'])) {
//
//                $_wpnonce = $_REQUEST['_wpnonce'];


        $request = stripslashes_deep($_REQUEST);

        $filter = $request['filter'];

        EZP_IBC_U::debug("ajax get contacts: $filter");

        $contacts = null;

        $contacts = EZP_IBC_Contact_Entity::get_all($filter);

        //EZP_IBC_U::debug_dump('contacts', $contacts);
        $encoded_contacts = json_encode($contacts);

        EZP_IBC_U::debug("encoded contacts $encoded_contacts");

        echo $encoded_contacts;
        die();
    }

    function ws_export_contacts()
    {
        EZP_IBC_U::debug("export contacts");

        //rsrtodo      if (isset($_REQUEST['_wpnonce']))
        {

            //rsr todo   $_wpnonce = $_REQUEST['_wpnonce'];
            //rsrtodo        if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers'))
            {
                $request = stripslashes_deep($_REQUEST);

                $stage = isset($request['stage']) ? $request['stage'] : -1;
                $event = isset($request['event']) ? $request['event'] : -1;
                $event_parameter = isset($request['event_parameter']) ? $request['event_parameter'] : -1;
                $event_range = isset($request['event_range']) ? $request['event_range'] : -1;

                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"contacts.csv\";");
                header("Content-Transfer-Encoding: binary");


                EZP_IBC_U::debug("exporting $stage, $event, $event_parameter, $event_range");
                $contacts = EZP_IBC_Report_Helper::get_filtered_contacts($stage, $event, $event_parameter, $event_range);

                $this->echo_contact_export($contacts);

                exit;
            }
// rsr todo           else
//            {
//
//                EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't properly match!");
//            }
//        } else
//        {
//
//            EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't exist!");
//        }
        }
    }

    function ws_get_debug_file()
    {
        //rsrtodo      if (isset($_REQUEST['_wpnonce']))
        {

            //rsr todo   $_wpnonce = $_REQUEST['_wpnonce'];
            //rsrtodo        if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers'))
            {
                $request = stripslashes_deep($_REQUEST);

                $stage = isset($request['stage']) ? $request['stage'] : -1;
                $event = isset($request['event']) ? $request['event'] : -1;
                $event_parameter = isset($request['event_parameter']) ? $request['event_parameter'] : -1;
                $event_range = isset($request['event_range']) ? $request['event_range'] : -1;

                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"debug.log\";");
                header("Content-Transfer-Encoding: binary");

                //$file_path = WP_CONTENT_DIR . '/debug.log';
                $file_path = ini_get('error_log');

                $handle = fopen($file_path, 'rb');
                //$handle = debug_fopen($file_path, 'r');

                
                if ($handle != false)
                {
                    $file_size = filesize($file_path);
                    
                    if($file_size > 0)
                    {
                        $contents = fread($handle, $file_size);
                    }
                    else
                    {
                        $contents = '';
                    }
                    
                    fclose($handle);
                }
                else
                {
                    echo "Error reading $file_path";
                }

                echo $contents;

                exit;
            }
// rsr todo           else
//            {
//
//                EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't properly match!");
//            }
//        } else
//        {
//
//            EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't exist!");
//        }
        }
    }

    function ws_export_list()
    {
        EZP_IBC_U::debug("export list");

        //rsrtodo      if (isset($_REQUEST['_wpnonce']))
        {

            //rsr todo   $_wpnonce = $_REQUEST['_wpnonce'];
            //rsrtodo        if (wp_verify_nonce($_wpnonce, 'easy-pie-cs-change-subscribers'))
            {
                $list_id = isset($_REQUEST['list']) ? (int) $_REQUEST['list'] : -1;

                $list = EZP_IBC_List_Entity::get_by_id($list_id);
                
                $filename = (($list == null) ? "list-$list_id.csv" : "list-$list->name.csv");
                
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"$filename\";");
                header("Content-Transfer-Encoding: binary");

                $request = stripslashes_deep($_REQUEST);

                


                if ($list != null)
                {
                    EZP_IBC_U::debug("ws export list $list_id");


                    $contacts = EZP_IBC_Contact_Entity::get_all_by_list_id($list_id);

                    $this->echo_contact_export($contacts);
                }
                else
                {
                    echo "Error exporting list. Couldn't find list $list_id";
                }

                exit;
            }
// rsr todo           else
//            {
//
//                EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't properly match!");
//            }
//        } else
//        {
//
//            EZP_IBC_U::debug("ws_export_all_subscribers: Security violation. Nonce doesn't exist!");
//        }
        }
    }

    private function echo_contact_export($contacts)
    {
        echo "Display Name, Last Name, First Name, Email Address, Score, Stage, Source, Date\r\n";
        foreach ($contacts as $contact)
        {
            /* @var $contact EZP_IBC_Contact_Entity */
            if ($contact->creation_timestamp != '')
            {
                //   $localized_date = date_i18n(get_option('date_format'), strtotime($subscriber->subscription_date));
                $creation_date_text = date('n/j/Y', strtotime($contact->creation_timestamp));
            }
            else
            {
                //   $localized_date = '';
                $creation_date_text = '';
            }

            $stage_string = EZP_IBC_Contact_Entity::get_stage_string($contact->stage);
            $display_name = $contact->get_display_name();

            echo "$display_name, $contact->last_name, $contact->first_name, $contact->email, $contact->score, $stage_string, $contact->referral_path, $creation_date_text\r\n";
        }
    }

    function ws_add_contacts_to_list()
    {
        $request = stripslashes_deep($_REQUEST);

        $list_id = $request['list_id'];
        $stage = $request['stage'];
        $event = $request['event'];
        $event_parameter = $request['event_parameter'];
        $event_range = $request['event_range'];

        EZP_IBC_U::debug("ws_add_contacts_to_list($list_id, $stage, $event, $event_parameter, $event_range)");
        $list = EZP_IBC_List_Entity::get_by_id($list_id);

        if ($list != null)
        {
            $contacts = EZP_IBC_Report_Helper::get_filtered_contacts($stage, $event, $event_parameter, $event_range);

            foreach ($contacts as $contact)
            {
                /* @var $contact EZP_IBC_Contact_Entity */
                $contact->add_to_list($list->id);
                $contact->save();
            }
        }
        else
        {
            EZP_IBC_U::debug("Tried adding contacts to non existing list. List id=$list_id");
            //rsr todo: ajax error handling
        }
    }

    function add_class_action($tag, $method_name)
    {

        return add_action($tag, array($this, $method_name));
    }
}