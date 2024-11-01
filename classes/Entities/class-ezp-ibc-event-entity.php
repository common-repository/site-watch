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

require_once(dirname(__FILE__) . '/class-ezp-ibc-standard-entity-base.php');

if (!class_exists('EZP_IBC_Event_Types'))
{

    abstract class EZP_IBC_Event_Types
    {
        const Page_Load = 0;
        const Form_Submitted = 1;
        const Button_Clicked = 2;
        const User_Event = 3;
        const WP_Login = 4;
        const Stage_Change = 5; /* Indicates important state change of the contact such as when two identities have merged */
        const Purchase = 6;

    }
}

if (!class_exists('EZP_IBC_Event_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Event_Entity extends EZP_IBC_Standard_Entity_Base
    {
        public $contact_id = -1;
        public $type = -1;
        public $timestamp;
        public $ip_address = '';
        public $parameter = ''; // Either Form ID, Button ID, URL, or User defined
        public $parameter2 = '';
        public $parameter3 = '';
        public $data = '';
        // Distinct object references the same table as other plugins
        public static $TABLE_NAME = 'easy_pie_ibc_events';

        function __construct()
        {

            parent::__construct(self::$TABLE_NAME);

            $this->timestamp = date("Y-m-d H:i:s");
        }

        public static function init_table()
        {

            $field_info = array();

            $field_info['contact_id'] = 'int';
            $field_info['type'] = 'int';
            $field_info['timestamp'] = 'datetime';
            $field_info['ip_address'] = 'varchar(32)';
            $field_info['parameter'] = 'text';
            $field_info['parameter2'] = 'text';
            $field_info['parameter3'] = 'varchar(255)';
            $field_info['data'] = 'text';


            $index_array = array();
            $index_array['contact_idx'] = 'contact_id';
            $index_array['parameter_idx'] = 'parameter(100), parameter2(100), parameter3(100)';
            $index_array['type_idx'] = 'type';

            self::generic_init_table($field_info, self::$TABLE_NAME, $index_array, 'bigint');
        }
        
        public static function get_worth($event_type, $parameter = '', $parameter2 = '', $parameter3 = '')
        {
            $worth = 0;
            
            $global = EZP_IBC_Global_Entity::get_instance();
            /* @var $global EZP_IBC_Global_Entity */
            
            switch($event_type)
            {
                case EZP_IBC_Event_Types::Page_Load:
                    $worth = (int)$global->page_view_event_worth;
                    break;
                    
                case EZP_IBC_Event_Types::Form_Submitted:
                    $worth = (int)$global->form_submit_event_worth;
                    break;
                
                case EZP_IBC_Event_Types::User_Event:
                    $user_event_type_id = $parameter;
                    
                    $user_event = EZP_IBC_User_Event_Type_Entity::get_by_id($user_event_type_id);
                    
                    /* @var $user_event EZP_IBC_User_Event_Type_Entity */
                    $worth = $user_event->worth;
                    break;
                
                case EZP_IBC_Event_Types::WP_Login:
                    $worth = (int)$global->login_event_worth;
                    break;
                
                case EZP_IBC_Event_Types::Stage_Change:
                    //return (int)$global->stage_change_event_worth;  // TODO consider from which to which
                    $worth = 0;
                    break;
                
                case EZP_IBC_Event_Types::Purchase:
                    $worth = (int)$global->purchase_event_worth;
                    break;
                
                default:
                    EZP_IBC_U::debug("Unknown event type " . $event_type);                   
            }
            
      //      EZP_IBC_U::debug("worth for $event_type is $worth");
            
            return $worth;
        }

        public static function get_all_where($where_clause = '', $order_by = '')
        {
            $order_by_text = '';
            $where_text = '';

            if ($order_by != '')
            {
                $order_by_text = "ORDER BY $order_by";
            }
            if ($where_clause != '')
            {
                $where_text = "WHERE $where_clause";
            }

            $events = self::get_all_objects(get_class(), self::$TABLE_NAME, $where_text, $order_by_text);

            return $events;
        }

        public static function get_limited_events($where_clause = '', $order_by_clause = '', $count)
        {
            $where_text = empty($where_clause) ? '' : "WHERE $where_clause";
            $order_by_text = empty($order_by_clause) ? '' : "ORDER BY $order_by_clause";

            $events = self::get_limited_objects(get_class(), self::$TABLE_NAME, $where_text, $order_by_text, $count);

            return $events;
        }

//        public static function get_recent_event_counts($num_minutes)
//        {
//            global $wpdb;
//
//            $table_name = $wpdb->prefix . self::$TABLE_NAME;
//
//            $current_time = time();
//            $time_threshold = $current_time - ($num_minutes * 60);
//            $time_threshold = EZP_IBC_U::ticks_to_standard_formatted($time_threshold);
//
//            $query_string = "SELECT count(id) as count, MINUTE(timestamp) as m from $table_name where timestamp > '$time_threshold' group by MINUTE(timestamp)";
//
//            //EZP_IBC_U::debug("$query_string");
//            $raw_counts = $wpdb->get_results($query_string);
//            $counts_by_min = array();
//
//            $loop_time = $current_time - ($num_minutes * 60);
//
//            for ($min_index = 0; $min_index < $num_minutes; $min_index++)
//            {                
//                EZP_IBC_U::debug("loop time $loop_time");
//                $minute = (int)date('i', $loop_time);
//                //$counts_by_min[$minute] = 0;
//                $counts_by_min[$min_index] = [$minute, 0];
//                
//                $loop_time += 60;
//            }
//            
//            foreach ($raw_counts as $raw_count)
//            {
//                $m = (int)$raw_count->m;
//                
//                $counts_by_min[$m] = (int) $raw_count->count;
//            }
//           
//            //rsr todo - reimplement
//            return $counts_by_min;
//        }

        public function get_virtual_name()
        {
            switch ($this->type)
            {
                case EZP_IBC_Event_Types::Page_Load:
                    return $this->get_page_load_event_virtual_name();
                    break;


                case EZP_IBC_Event_Types::Form_Submitted:
                    // Contains form name or #form_id appended with the url it came from
                    return $this->get_form_submitted_event_virtual_name();
                    break;

                case EZP_IBC_Event_Types::User_Event:
                    return $this->get_user_event_virtual_name();
                    break;

                case EZP_IBC_Event_Types::Purchase:
                    return $this->get_purchase_event_virtual_name();
                    break;

                case EZP_IBC_Event_Types::WP_Login:
                    return $this->get_wp_login_event_virtual_name();
                    break;

                case EZP_IBC_Event_Types::Stage_Change:
                    return $this->get_stage_change_virtual_name();
                    break;

                default:
                    return EZP_IBC_U::__('Unknown');
            }
        }

        public function get_display_details()
        {
            switch ($this->type)
            {
                case EZP_IBC_Event_Types::Form_Submitted:
                    return $this->get_form_submitted_event_display_details();

                case EZP_IBC_event_Types::Purchase:
                    return $this->get_purchase_event_display_details();

                case EZP_IBC_Event_Types::User_Event:
                    return $this->get_user_event_display_details();

                default:
                    return '';
            }
        }

        private function get_user_event_display_details()
        {
            if (!empty($this->data))
            {
                $html = stripcslashes($this->data);

                $html = htmlentities($html);

                $details = "<p style='margin-left:14px'>$html</p>";

                return $details;
            }
            else
            {
                return '';
            }
        }

        private function get_form_submitted_event_display_details()
        {
            $fields = json_decode($this->data);

            $details = __('<p style="margin-left:14px">') . __('ID') . " = $this->parameter</p>";

            //if(is_array($fields))
            if (!empty($fields))
            {
                //EZP_IBC_U::debug_object("fields is an array", $fields);
                foreach ($fields as $field_name => $data)
                {
                    $details .= "<p style='margin-left:14px'><span class='easy-pie-details-label'>$data->label</span> = <span class='easy-pie-details-value'>$data->value</span></p>";
                }
            }

            return $details;
        }

        public static function get_type_string($event_type)
        {
            $event_type_string = __('Unknown Event');

            switch ($event_type)
            {
                case EZP_IBC_Event_Types::Page_Load:
                    $event_type_string = __('Page Load');
                    break;

                case EZP_IBC_Event_Types::Form_Submitted:
                    $event_type_string = __('Form Submission');
                    break;

                case EZP_IBC_Event_Types::Button_Clicked:
                    $event_type_string = __('Button Click');
                    break;

                case EZP_IBC_Event_Types::Stage_Change:
                    $event_type_string = __('Lifecycle Change');
                    break;

                case EZP_IBC_Event_Types::Purchase:
                    $event_type_string = __('Purchase');
                    break;

                case EZP_IBC_Event_Types::WP_Login:
                    $event_type_string = __('WordPress Login');
                    break;

                case EZP_IBC_Event_Types::User_Event:
                    $event_type_string = __('User Event');
                    break;
            }

            return $event_type_string;
        }

        public static function get_count_where($where_clause = null)
        {
            return parent::get_count_where_base(self::$TABLE_NAME, $where_clause);
        }

        public static function get_counts_by_field($field, $where_clause = null)
        {
            return parent::get_counts_by_field_base(self::$TABLE_NAME, $field, $where_clause);
        }

        public static function get_counts_by_fields($fields, $where_clause = null)
        {
            return parent::get_counts_by_fields_base(self::$TABLE_NAME, $fields, $where_clause);
        }

        private function get_stage_change_virtual_name()
        {
            $old_stage = EZP_IBC_Contact_Entity::get_stage_string($this->parameter2);
            $new_stage = EZP_IBC_Contact_Entity::get_stage_string($this->parameter);

            return "$old_stage -> $new_stage";
        }

        private function get_wp_login_event_virtual_name()
        {
            // Parameter contains wordpress id for login events
            $wp_id = (int) $this->parameter;

            $user = get_userdata($wp_id);

            $name_string = __('User') . ' ';

            if ($user != false)
            {
                $name_string .= "'$user->user_login'";
            }
            else
            {
                $name_string .= 'with user id' . $wp_id;
            }

            return $name_string;
        }

        public function get_form_submitted_event_virtual_name()
        {
            $page_name = EZP_IBC_U::get_page_name_from_url($this->parameter2);

            return __('Form on ') . $page_name;

            //return $this->parameter . ' (' . $this->parameter2 . ')';
        }

        public function get_color()
        {
            switch ($this->type)
            {
                case EZP_IBC_Event_Types::Page_Load:
                    return "#000";
                    break;

                case EZP_IBC_Event_Types::Form_Submitted:
                    return "navy";
                    break;

                case EZP_IBC_Event_Types::User_Event:
                    return "forestgreen";
                    break;

                case EZP_IBC_Event_Types::WP_Login:
                    return "sienna";
                    break;

                case EZP_IBC_Event_Types::Stage_Change:
                    return "maroon";
                    break;

                case EZP_IBC_Event_Types::Purchase:
                    return "seagreen";
                    break;

                default:
                    return "grey";
                    break;
            }
        }

        private function get_purchase_event_virtual_name()
        {
            // data contains line item information and parameter contains product id

            $order_id = (int) $this->parameter;
            $product_id = (int) $this->parameter2;
            $ecommerce_system_type = (int) $this->parameter3;

            $line_item = EZP_IBC_Order_Line_Item::get_instance_from_event_data($this->data);

            $name = $line_item->product_detail->name;

            $virtual_name = $name;

            if ($line_item->quantity != 1)
            {
                $virtual_name .= " (x$line_item->quantity)";
            }

            return $virtual_name;
        }

        private function get_purchase_event_display_details()
        {
            $order_id = (int) $this->parameter;
            $product_id = (int) $this->parameter2;
            $ecommerce_system_type = (int) $this->parameter3;

            $line_item = EZP_IBC_Order_Line_Item::get_instance_from_event_data($this->data);

            $system_type = $ecommerce_system_type == EZP_IBC_ECommerce_Modes::WOO_COMMERCE ? __('(WC)') : '';

            $details = "<p style='margin-left:14px'><span class='easy-pie-details-label'>" . __('Order ID') . "</span> = <span class='easy-pie-details-value'>$order_id $system_type</span></p>";
            $details .= "<p style='margin-left:14px'><span class='easy-pie-details-label'>" . __('Line Item Cost') . "</span> = <span class='easy-pie-details-value'>$line_item->total</span></p>";
            return $details;
        }

        private function get_user_event_virtual_name()
        {
            // Parameter for user events contains is of the user event type
            $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id((int) $this->parameter);

            if ($user_event_type != null)
            {
                return $user_event_type->name;
            }
            else
            {
                return sprintf(__("Unknown User Event %1$s", $this->parameter));
            }
        }

        private function get_page_load_event_virtual_name()
        {
            return EZP_IBC_U::get_page_name_from_url($this->parameter);
        }

        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);
        }

        public static function delete_by_id($id)
        {

            self::delete_by_id_and_table($id, self::$TABLE_NAME);
        }

//        
//        public static function get_all()
//        {
//            return self::get_all_objects(get_class(), self::$TABLE_NAME);
//        }
////        
//        /**
//         * 
//         * @param type $id
//         * @return EZP_IBC_Contact_Entity
//         */
//        public static function get_by_id($id)
//        {
//            return self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);
//        }
        public static function get_all_by_contact_id($contact_id, $order_by = 'timestamp asc')
        {
            //return self::get_all_by_field_and_type('contact_id', $contact_id, get_class(), self::$TABLE_NAME, 'timestamp desc');
            return self::get_all_by_field_and_type('contact_id', $contact_id, get_class(), self::$TABLE_NAME, $order_by);
        }

        public static function delete_by_contact_id($contact_id)
        {
            self::delete_by_field_and_table('contact_id', $contact_id, self::$TABLE_NAME);
        }

        public static function delete_by_user_event_type_id($user_event_type)
        {
            /* In the case of user events parameter is the user event type */
            $event_type = EZP_IBC_Event_Types::User_Event;

            self::delete_where(self::$TABLE_NAME, "type=$event_type and parameter=$user_event_type");
        }

        public static function delete_all()
        {
            parent::delete_all_base(self::$TABLE_NAME, '');
        }
    }
    //   EZP_Contact_Entity::init_class();
}
?>