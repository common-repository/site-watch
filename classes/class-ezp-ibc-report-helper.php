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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY .  '/classes/Entities/class-ezp-ibc-event-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY .  '/classes/Entities/class-ezp-ibc-contact-entity.php');

if (!class_exists('EZP_IBC_Report_Range_Types')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    abstract class EZP_IBC_Report_Range_Types 
    {    
        const Disabled = -1;
        const Today = 0;
        const Yesterday = 1;
        const This_Week = 2;
        const Last_Week = 3;        
        const Last_Month = 4;
        const Last_7_Days = 5;
        const Last_30_Days = 6;
    }
}

if (!class_exists('EZP_IBC_Report_Types')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    abstract class EZP_IBC_Report_Virtual_Event_Types 
    {    
        const Is_New_Visitor = -2;  // creation date in range
        const Experienced_Lead_Conversion = -3; // There is a stage change event of lead in the range 
        const Bought_A_Product_For_First_Time = -4; // There is a stage change event of customer in the range
    }
}

if (!class_exists('EZP_IBC_Report_Helper')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Report_Helper {       
        
        public static $last_query = '';
        
        public static function get_filtered_contacts($stage, $event_id, $event_parameter, $event_range, $search = null)
        {            
            $from = self::get_timestamp_from_report_range($event_range, true);
            $to = self::get_timestamp_from_report_range($event_range, false);
                                                
            switch($event_id)
            {
                case -1:
                    // No event filter on
                    return self::get_contacts($stage, $search);
                    break;
                
//                case EZP_IBC_Report_Virtual_Event_Types::Is_New_Visitor:
//                    return self::get_new_contacts($stage, $from, $to, $search);
//                    break;
//                    
//                case EZP_IBC_Report_Virtual_Event_Types::Experienced_Lead_Conversion:
//                    return self::get_contacts_with_lead_conversions($stage, $from, $to, $search);
//                    break;
//                    
//                case EZP_IBC_Report_Virtual_Event_Types::Bought_A_Product_For_First_Time:
//                    return self::get_contacts_who_bought_first_product($stage, $from, $to, $search);
//                    break;
                    
                default:
                    //echo "params: $stage, $event_id, $event_parameter, $from, $to, $search $event_range";
                    return self::get_contacts_who_experienced_event($stage, $event_id, $event_parameter, $from, $to, $search);
                    break;                    
            }
        }
        
        private static function get_contacts($stage, $search)
        {
            if(($stage == -1) && ($search == null))
            {
                $contacts = EZP_IBC_Contact_Entity::get_all();
            }
            else
            {
                $where_clause = self::get_contact_where_clause($stage, $search);

                $contacts = EZP_IBC_Contact_Entity::get_all_where($where_clause);
            }
            
            if(!empty($search))
            {
                self::append_wp_contacts_from_search($contacts, $search);
            }
            
            return $contacts;
        }
        
        public static function get_timestamp_from_report_range($report_range_type, $is_from_timestamp)
        {
            $now = time();
    
            $from = $now - (24 * 60 * 60);
            $to = $now;

            switch($report_range_type)
            {                
                case EZP_IBC_Report_Range_Types::Disabled:
                    $from = 0;
                    $to = 2147483647;
                    break;
                
                case EZP_IBC_Report_Range_Types::Yesterday:
                    $from = $now - (48 * 60 * 60);
                    $to = $now - (24 * 60 * 60);
                    break;

                case EZP_IBC_Report_Range_Types::This_Week:
                    $this_week_start = strtotime('last sunday');

                    $from = $this_week_start;
                    $to = $now;
                    break;  
                
                case EZP_IBC_Report_Range_Types::Last_Week:
                    $last_week_start = strtotime('-1 week');
                    $last_week_start = strtotime('last sunday', $last_week_start);
                    $this_week_start = strtotime('last sunday');

                    $from = $last_week_start;
                    $to = $this_week_start;
                    break;              

                case EZP_IBC_Report_Range_Types::Last_Month:
                    $from = strtotime('first day of last month');
                    $to = strtotime('last day of last month');
                    break;              

                case EZP_IBC_Report_Range_Types::Last_7_Days:
                    $from = strtotime('-1 week');
                    $to = $now;
                    break;

                case EZP_IBC_Report_Range_Types::Last_30_Days:
                    $from = strtotime('-30 days');
                    $to = $now;
                    break;
            }
                        
            
            //echo "from to $from $to";
            if($is_from_timestamp)
            {
                return date("Y-m-d H:i:s", $from);
            }
            else
            {
                return date("Y-m-d H:i:s", $to); 
            }
        }
        
        public static function get_stage_text($stage)
        {
            $text = __('Unknown Stage');
            
            switch($stage)
            {
                case EZP_IBC_Contact_Stages::Prospect:
                    $text = __('Prospects');
                    break;
                
                case EZP_IBC_Contact_Stages::Lead:
                    $text = __('Leads');
                    break;
                
                case EZP_IBC_Contact_Stages::Customer:
                    $text = __('Customers');
                    break;
            }
            
            return $text;
        }
        
        public static function get_event_text($event, $event_parameter)
        {
            $text = __('unknown event');                      
                        
            switch($event)
            {                
//                case EZP_IBC_Report_Virtual_Event_Types::Is_New_Visitor:
//                    $text = __('visited site for the first time');
//                    break;
//                
//                case EZP_IBC_Report_Virtual_Event_Types::Experienced_Lead_Conversion:
//                    $text = __('became leads');
//                    break;
//                
//                case EZP_IBC_Report_Virtual_Event_Types::Bought_A_Product_For_First_Time:
//                    $text = __('became new customers');
//                    break;  
                
                
                case EZP_IBC_Event_Types::User_Event:
                    $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id((int)$event_parameter);
                    
                    $text = __('user event');
                            
                    if($user_event_type != null)
                    {
                        $text .= " '$user_event_type->name'";
                    }
                    
                    break;
                    
                default:
                    $event_type_string = EZP_IBC_Event_Entity::get_type_string($event);
                    
                    $text = $event_type_string;
                    break;                        
            }
            
            return $text;
        }
        
        public static function get_range_text($event_range)
        {
            $text = __('unknown time period');
            
            switch($event_range)
            {                
                case EZP_IBC_Report_Range_Types::Today:
                    $text = 'today';
                    break;
                
                case EZP_IBC_Report_Range_Types::Yesterday:
                    $text = 'yesterday';
                    break;
                
                case EZP_IBC_Report_Range_Types::This_Week:
                    $text = 'this week';
                    break;
                
                case EZP_IBC_Report_Range_Types::Last_Week:
                    $text = 'last week';
                    break;
                
                case EZP_IBC_Report_Range_Types::Last_Month:
                    $text = 'last month';
                    break;
                
                case EZP_IBC_Report_Range_Types::Last_7_Days:
                    $text = 'last 7 days';
                    break;
                
                case EZP_IBC_Report_Range_Types::Last_30_Days:
                    $text = 'last 30 days';
                    break;
            }
            
            return $text;
        }  
        
                 
        private static function get_new_contacts($stage, $from, $to, $search)
        {               
            $contact_where_clause = self::get_contact_where_clause($stage, $search);
            
            if($contact_where_clause != '')
            {
                $contact_where_claues .= ' AND ';
            }
            
            $contact_where_clause .= "(creation_timestamp BETWEEN '$from' and '$to')";
            
            return EZP_IBC_Contact_Entity::get_all_where($contact_where_clause);
        }
        
        private static function get_contact_where_clause($stage, $search)
        {
            $contact_where_clause = '';
            $stage_where_clause = '';            
            $search_where_clause = '';
            
            if($search != null)
            {
                $search_where_clause = "first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%'";
                
                if(is_numeric($search))
                {
                    $search_where_clause .= " OR id=$search";                                        
                }
                
                EZP_IBC_U::debug("search where clause=$search_where_clause");
            }
                        
            if($stage != -1)
            {            
                $stage_where_clause = "stage = $stage";
            }
            
            if($stage != -1 || $search != null)
            {                                
                if($search != null)
                {
                    $contact_where_clause = $search_where_clause;
                    
                    if($stage != -1)
                    {
                        $contact_where_clause .= " AND ";
                    }
                }
                
                if($stage != -1)
                {
                    $contact_where_clause .= $stage_where_clause;
                }
                
                $contact_where_clause = "($contact_where_clause)";
            }
            
         //   EZP_IBC_U::debug("contact_where_clause: $contact_where_clause");
            
            return $contact_where_clause;
        }
        
//        private static function get_contacts_with_lead_conversions($stage, $from, $to, $search)
//        {                                         
//            $stage_change_event_type = EZP_IBC_Event_Types::Stage_Change;
//            $transition_type = EZP_IBC_Contact_Stages::Lead;
//            
//            $contact_where_clause = self::get_contact_where_clause($stage, $search);
//            $event_where_clause = "(type=$stage_change_event_type) AND (parameter = '$transition_type') AND (timestamp BETWEEN '$from' AND '$to')";
//            
//            return EZP_IBC_Contact_Entity::get_by_contact_event_where($contact_where_clause, $event_where_clause);
//        }
//        
//        private static function get_contacts_who_bought_first_product($stage, $from, $to, $search)
//        {
//            $stage_change_event_type = EZP_IBC_Event_Types::Stage_Change;
//            $transition_type = EZP_IBC_Contact_Stages::Customer;
//            
//            $contact_where_clause = self::get_contact_where_clause($stage, $search);
//            $event_where_clause = "(type=$stage_change_event_type) AND (parameter = $transition_type) AND (timestamp BETWEEN '$from' AND '$to')";
//            
//            return EZP_IBC_Contact_Entity::get_by_contact_event_where($contact_where_clause, $event_where_clause);
//        }
        
        private static function get_contacts_who_experienced_event($stage, $event_id, $event_parameter, $from, $to, $search)
        {
            $contact_where_clause = self::get_contact_where_clause($stage, $search);
            $event_where_clause = "(type=$event_id) AND ";
            
            if($event_parameter != -1)
            {
                $event_where_clause .= "(parameter = '$event_parameter') AND ";
            }
            
            $event_where_clause .= "(timestamp BETWEEN '$from' AND '$to')";
            
            $contacts =  EZP_IBC_Contact_Entity::get_by_contact_event_where($contact_where_clause, $event_where_clause);
            
            if(!empty($search))
            {
                self::append_wp_contacts_from_search($contacts, $search);
            }
            
            return $contacts;
        }
        
        private static function append_wp_contacts_from_search(&$contacts, $search)
        {                                    
            // RSR TODO: First retrieve list of all contacts with non wp -1
            $wp_contacts = EZP_IBC_Contact_Entity::get_all_where('wpid <> -1');
                     
            $search = strtolower($search);
            foreach($wp_contacts as $wp_contact)
            {
                $matches = false;
                
                $user_data = get_userdata($wp_contact->wpid);

                if ($user_data != false)
                {
                    if(!empty($user_data->display_name))
                    {
                        $lower_display = strtolower($user_data->display_name);
                        if(strpos($lower_display, $search) != false) 
                        {
                            $matches = true;
                        }
                    }
                    
                    if(!empty($user_data->user_login))
                    {
                        $lower_user_login = strtolower($user_data->user_login);
                        
                        if(strpos($lower_user_login, $search) != false) 
                        {
                            $matches = true;
                        }
                    }
                }   
                
                if($matches)            
                {
                    $insert = true;
                    
                    foreach($contacts as $contact)
                    {
                        if($contact->id == $wp_contact->id)
                        {
                            $insert = false;
                            break;
                        }
                    }
                    
                    if($insert)
                    {
                        array_push($contacts, $wp_contact);
                    }
                }
            }
        }
    }
}
?>