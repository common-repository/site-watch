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
require_once(dirname(__FILE__) . '/class-ezp-ibc-public-id-entity.php');

if (!class_exists('EZP_IBC_Contact_Type_Types'))
{

    abstract class EZP_IBC_Contact_Stages
    {
        const Prospect = 0;
        const Lead = 1;
        const Customer = 2;

    }
}

if (!class_exists('EZP_IBC_Contact_Default_Display_Names'))
{

    class EZP_IBC_Contact_Default_Display_Names
    {
        public static $stage_strings = array();

        static function init()
        {
            self::$stage_strings[EZP_IBC_Contact_Stages::Prospect] = __('Prospect');
            self::$stage_strings[EZP_IBC_Contact_Stages::Lead] = __('Lead');
            self::$stage_strings[EZP_IBC_Contact_Stages::Customer] = __('Customer');
        }
    }
    EZP_IBC_Contact_Default_Display_Names::init();
}

if (!class_exists('EZP_IBC_Country_Codes'))
{

    abstract class EZP_IBC_Country_Codes
    {
        const United_States = "US";

    }
}

//if (!class_exists('EZP_IBC_Field_Ids'))
//{
//    abstract class EZP_IBC_Field_Ids
//    {
//        const Last_Name = -2;
//        const First_Name = -3;
//        const Email = -4;
//        const Phone = -5;
//        const Address = -6;
//        const City = -7;
//        const State_Region = -8;
//        const Zip_Code = -9;
//        const Notes = -10;
//        const Type = -11;
//        const Creation_Timestamp = -12;
//        const Last_Event_Timestamp = -13;
//        const Last_Browser = -14;
//        const Last_IP = -15;
//    }
//}

if (!class_exists('EZP_IBC_Contact_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Contact_Entity extends EZP_IBC_Standard_Entity_Base
    {
        public $wpid = -1;
        //rsr may not want this as 'standard' although nice to have as custom  public $portrait_url = "";
        public $last_name = '';
        public $first_name = '';
        public $email = '';
        public $phone = '';
        public $address = '';
        public $city = '';
        public $state_region = '';
        public $zip_code = '';
        public $country_code = EZP_IBC_Country_Codes::United_States;
        public $notes = '';
        public $stage = EZP_IBC_Contact_Stages::Prospect;
        public $referral_path;
        public $list_ids = '';
        public $last_activity_timestamp = '';
        public $last_ip_address = '';
        public $creation_timestamp;
        public $score = 0;
        //    public $meta = '';

        public static $TABLE_NAME = "easy_pie_ibc_contacts";

        function __construct()
        {
            parent::__construct(self::$TABLE_NAME);

            $this->creation_timestamp = date("Y-m-d H:i:s");
            $this->last_activity_timestamp = date("Y-m-d H:i:s");
            $this->referral_path = __('Not Set');

            $this->verifiers['last_name'] = new EZP_IBC_Length_Verifier(255, __("Last name can't be longer than 255 characters"));
            $this->verifiers['first_name'] = new EZP_IBC_Length_Verifier(255, __("First name can't be longer than 255 characters"));
            $this->verifiers['email'] = new EZP_IBC_Length_Verifier(255, __("Email address can't be longer than 255 characters"));
            $this->verifiers['phone'] = new EZP_IBC_Length_Verifier(255, __("Phone number can't be longer than 255 characters"));
            $this->verifiers['address'] = new EZP_IBC_Length_Verifier(255, __("Address can't be longer than 255 characters"));
            $this->verifiers['city'] = new EZP_IBC_Length_Verifier(255, __("City can't be longer than 255 characters"));
            $this->verifiers['state_region'] = new EZP_IBC_Length_Verifier(255, __("State/region can't be longer than 255 characters"));
            $this->verifiers['zip_code'] = new EZP_IBC_Length_Verifier(255, __("Zip code can't be longer than 255 characters"));
            $this->verifiers['country_code'] = new EZP_IBC_Length_Verifier(2, __("Country code can't be longer than 2 characters"));
            $this->verifiers['notes'] = new EZP_IBC_Length_Verifier(1024, __("Notes can't be longer than 1024 characters"));
        }

        public function update_last_activity_timestamp($ip_address)
        {
            if (strstr($ip_address, ', '))
            {

                $ips = explode(', ', $ip_address);
                $ip_address = $ips[0];
            }

            $this->last_activity_timestamp = date("Y-m-d H:i:s");
            $this->last_ip_address = $ip_address;


            EZP_IBC_U::debug("setting timestamp, ip address = $ip_address");
        }

        public static function init_table()
        {
            $field_info = array();

            $field_info['last_name'] = 'varchar(255)';
            $field_info['first_name'] = 'varchar(255)';
            $field_info['email'] = 'varchar(255)';
            $field_info['phone'] = 'varchar(255)';
            $field_info['address'] = 'varchar(255)';
            $field_info['city'] = 'varchar(255)';
            $field_info['state_region'] = 'varchar(255)';
            $field_info['zip_code'] = 'varchar(50)';
            $field_info['country_code'] = 'char(2)';
            $field_info['notes'] = 'text';
            $field_info['stage'] = 'int';
            $field_info['list_ids'] = 'varchar(255)';
            $field_info['wpid'] = 'bigint';
            $field_info['referral_path'] = 'text';

            $field_info['creation_timestamp'] = 'datetime';
            $field_info['last_activity_timestamp'] = 'datetime';
            $field_info['last_ip_address'] = 'varchar(32)';
            $field_info['score'] = 'int DEFAULT 0';

            $index_array = array();
            $index_array['wpid_idx'] = 'wpid';
            $index_array['email_idx'] = 'email';


            //$index_array['meta'] = 'text';

            self::generic_init_table($field_info, self::$TABLE_NAME, $index_array);
        }

        public function rescore()
        {
            EZP_IBC_U::debug("re-scoring contact $this->first_name $this->last_name");

            $this->score = 0;

            $events = EZP_IBC_Event_Entity::get_all_by_contact_id($this->id);

            foreach ($events as $event)
            {
                /* @var $event EZP_IBC_Event_Entity */
                $this->score += EZP_IBC_Event_Entity::get_worth($event->type, $event->parameter, $event->parameter2, $event->parameter3);
            }
        }

        public static function score_all()
        {
            $contacts = self::get_all('', false);

            foreach ($contacts as $contact)
            {
                EZP_IBC_U::debug("scoring contact $contact->first_name $contact->last_name");
                /* @var $contact EZP_IBC_Contact_Entity */
                $contact->score = 0;

                $events = EZP_IBC_Event_Entity::get_all_by_contact_id($contact->id);

                foreach ($events as $event)
                {
                    /* @var $event EZP_IBC_Event_Entity */
                    $contact->score += EZP_IBC_Event_Entity::get_worth($event->type, $event->parameter, $event->parameter2, $event->parameter3);
                }

                $contact->save();
            }
        }

        public function transition($new_stage)
        {
            if ($new_stage > $this->stage)
            {
                $old_stage = $this->stage;
                $this->stage = $new_stage;

                //    $this->add_to_event_count(EZP_IBC_Event_Types::Stage_Change);

                $this->score += EZP_IBC_Event_Entity::get_worth(EZP_IBC_Event_Types::Stage_Change, $new_stage, $old_stage);

                $this->save();

                $event = new EZP_IBC_Event_Entity();

                $event->contact_id = $this->id;
                $event->type = EZP_IBC_Event_Types::Stage_Change;
                $event->parameter = $new_stage;
                $event->parameter2 = $old_stage;

                $event->save();

                //     $this->send_transition_notification($this->stage, $new_stage);

                EZP_IBC_U::debug("saved stage change");
            }
            else
            {
                EZP_IBC_U::debug("Attempting to change stage of contact $this->id from $this->stage to $new_stage but was higher so nothing changed...");
            }
        }

        public function get_last_hostname()
        {
            $hostname = __('Unknown');


            if (!empty($this->last_ip_address))
            {
                $transient_name = "ezp_ip_$this->last_ip_address";
                $transient_name = hash("md5", $transient_name);

                $hostname = get_transient($transient_name);
                if ($hostname === false)
                {

                    $hostname = gethostbyaddr($this->last_ip_address);

                    if ($hostname === false)
                    {
                        $hostname = __('Unknown');
                        EZP_IBC_U::debug("hostname fale so set to unknown");
                    }

                    // Set 1 hour transient with the name
                    set_transient($transient_name, $hostname, 60 * 60);
                }
            }

            return $hostname;
        }

        public function get_display_name()
        {
            $display_name = '';

            if ($this->first_name == '' && $this->last_name == '')
            {
                if ($this->wpid != -1)
                {
                    $user_data = get_userdata($this->wpid);

                    if ($user_data != false)
                    {
                        if (empty($user_data->display_name))
                        {
                            $display_name = $user_data->user_login;
                        }
                        else
                        {
                            $display_name = $user_data->display_name;
                        }
                    }
                    else
                    {
                        if (empty($this->email))
                        {
                            $display_name = EZP_IBC_Contact_Default_Display_Names::$stage_strings[$this->stage] . " " . $this->id;
                        }
                        else
                        {
                            $display_name = $this->email;
                        }
                    }
                }
                else
                {
                    if (empty($this->email))
                    {
                        $display_name = EZP_IBC_Contact_Default_Display_Names::$stage_strings[$this->stage] . " " . $this->id;                        
                    }
                    else
                    {
                        $display_name = $this->email;
                    }
                }
            }
            else if ($this->first_name != '' && $this->last_name == '')
            {
                $display_name = $this->first_name;
            }
            else if ($this->first_name == '' && $this->last_name != '')
            {
                $display_name = $this->last_name;
            }
            else
            {
                $display_name = "$this->first_name $this->last_name";
            }

            if ($this->wpid != -1)
            {
                $display_name = "$display_name (WP)";
            }

            return $display_name;
        }

        public function set_referer($referrer)
        {
            if ($referrer != null)
            {
                $this->referral_path = $referrer;
            }
            else
            {
                $this->referral_path = '';
            }
        }

        public static function get_stage_string($stage)
        {
            switch ($stage)
            {
                case EZP_IBC_Contact_Stages::Prospect:
                    return EZP_IBC_U::__('Prospect');
                    break;

                case EZP_IBC_Contact_Stages::Lead:
                    return EZP_IBC_U::__('Lead');
                    break;

                case EZP_IBC_Contact_Stages::Customer:
                    return EZP_IBC_U::__('Customer');
                    break;

                default:
                    return EZP_IBC_U::__('Unknown Stage');
                    break;
            }
        }

//        private function send_transition_notification($from_stage, $to_stage)
//        {
//            $from_stage_string = self::get_stage_string($from_stage);
//            $to_stage_string = self::get_stage_string($to_stage);
//            
//            /* @var $contact EZP_IBC_Contact_Entity */
//            $global = EZP_IBC_Global_Entity::get_instance();
//                        
//            if (!empty($global->notify_email))
//            {
//                $display_name = $this->get_display_name();
//                
//                $info = "Contact $display_name ($this->id) transitioned to from stage $from_stage_string to $to_stage_string.";
//                
//                EZP_IBC_U::debug($info);
//                
//                $subject = $info;
//                $message = $info;
//
//                if (wp_mail($global->notify_email, $subject, $message))
//                {
//                    EZP_IBC_U::debug($info . 'successfully sent');
//                }
//                else
//                {
//                    EZP_IBC_U::debug($info . 'failed to be sent');
//                }
//            }
//            else
//            {
//                $display_name = $this->get_display_name();
//                
//                EZP_IBC_U::debug("Tried to send email regarding form $form_id submission by $display_name but no notification email address configured.");
//            }
//        
//        }

        public function set_post_variables($post)
        {
            $list_ids = array();

            if (!empty($post['list_ids']))
            {
                foreach ($post['list_ids'] as $list_id)
                {
                    EZP_IBC_U::ddebug("pushing list $list_id onto contact");
                    array_push($list_ids, (int) $list_id);
                }
            }

            $this->list_ids = json_encode($list_ids);

            return parent::set_post_variables($post);
        }

        public static function get_count_where($where_clause = null)
        {
            return parent::get_count_where_base(self::$TABLE_NAME, $where_clause);
        }

        public static function get_counts_by_field($field, $where_clause = null)
        {
            return parent::get_counts_by_field_base(self::$TABLE_NAME, $field, $where_clause);
        }

        public function add_to_list($list_id)
        {
            $list_ids = $this->get_list_ids();

            if (in_array($list_id, $list_ids) == false)
            {
                array_push($list_ids, $list_id);

                $this->list_ids = json_encode($list_ids);

                $display_name = $this->get_display_name();

                EZP_IBC_U::debug("Added $list_id to $display_name. Lists now are $this->list_ids");
            }
        }

        public function remove_from_list($list_id)
        {
            $list_ids = $this->get_list_ids();

            if (in_array($list_id, $list_ids))
            {
                $list_ids = array_diff($list_ids, array($list_id));

                $this->list_ids = json_encode($list_ids);
            }
        }

        public function get_list_ids()
        {
            if ($this->list_ids)
            {
                return json_decode($this->list_ids, true);
            }
            else
            {
                return array();
            }
        }

        public function get_lists()
        {
            $lists = array();

            if ($this->list_ids != '')
            {
                $list_ids = json_decode($this->list_ids, true);

                foreach ($list_ids as $list_id)
                {
                    $list = EZP_IBC_List_Entity::get_by_id($list_id);

                    if ($list != null)
                    {
                        array_push($lists, $list);
                    }
                    else
                    {
                        EZP_IBC_U::debug("Contact $this->id is part of non existent list $list_id");
                    }
                }
            }

            return $lists;
        }

        /**
         * When an event occurs we need to properly set up the contact and pid
         * 
         * @returns EZP_IBC_Contact_Entity
         */
        public static function prep_contact_for_event($server, $cookie, $event_type, $data = null, $parameter = null, $parameter2 = null, $parameter3 = null)
        {
            $contact_data = new EZP_IBC_Contact_Entity();
            $referrer = null;
            if ($event_type == EZP_IBC_Event_Types::Page_Load)
            {
                $referrer = $parameter2;
            }

            $contact = self::get_by_cookie($cookie, $referrer);

            if ($event_type == EZP_IBC_Event_Types::Form_Submitted)
            {
                self::populate_form_contact_data($contact_data, $data);
            }
            else if ($event_type == EZP_IBC_Event_Types::WP_Login)
            {
                $contact_data->wpid = $data;
            }

            if ($contact == null)
            {
                EZP_IBC_U::debug('Contact not found for pid');

                $contact = self::get_contact_by_data($contact_data);

                if ($contact == null)
                {
                    // Brand new contact                  
                    $contact = new EZP_IBC_Contact_Entity();

                    self::set_contact_values($contact_data, $contact);

                    $contact->set_referer($referrer);

                    if ($contact->wpid != -1)
                    {
                        $contact->transition(EZP_IBC_Contact_Stages::Lead);
                    }

                    $contact->save();

                    EZP_IBC_U::debug("Created CONTACT $contact->id");

                    $public_id = new EZP_IBC_Public_ID_Entity();

                    $public_id->contact_id = $contact->id;

                    $public_id->save();

                    EZP_IBC_U::debug("Created cookie $public_id->pid for CONTACT $contact->id (public id $public_id->id)");
                }
                else
                {
                    // We already know this person by email so simply add the new cookie to their user
                    $public_id = new EZP_IBC_Public_ID_Entity();

                    $public_id->contact_id = $contact->id;

                    $public_id->save();

                    $changed = self::set_contact_values($contact_data, $contact);

                    if ($changed)
                    {
                        $contact->save();
                    }

                    EZP_IBC_U::debug("We already know the user by email $email so creating cookie  cookie $public_id->pid for CONTACT $contact->id (public id $public_id->id)");
                }
            }
            else
            {
                EZP_IBC_U::debug("found contact by cookie");

                $changed = self::set_contact_values($contact_data, $contact);

                // need to look it up by exising wpid OR email that has been submitted OR email of the wordpress id that was submitted
                $data_contact = self::get_contact_by_data($contact_data);

                if ($data_contact == null)
                {
                    EZP_IBC_U::debug("No contact found by data contact so just updating existing one");

                    // There isn't already an existing contact so just save the collected values in the exisint one we've identified                   
                    if ($changed)
                    {
                        $contact->save();
                    }
                }
                else
                {
                    EZP_IBC_U::debug("Found a contact based on data submitted. ID=$data_contact->id");

                    if ($data_contact->id == $contact->id)
                    {
                        EZP_IBC_U::debug("Data contact id same as cookie contact id so just saving");
                        if ($changed)
                        {
                            $contact->save();
                        }
                    }
                    else
                    {
                        EZP_IBC_U::debug("Data contact id different from cookie contact id so combining");

                        // $contact is from the cookie, $data_contact from submitted data so combine the two.

                        self::combine_contacts($contact, $data_contact);

                        $contact = $data_contact;

                        if ($contact->wpid != -1)
                        {
                            $contact->transition(EZP_IBC_Contact_Stages::Lead);
                        }
                    }
                }
            }

            if ($contact_data->email != '')
            {
                EZP_IBC_U::debug("contact transition because of new email $contact_data->email. event type $event_type");
                $contact->transition(EZP_IBC_Contact_Stages::Lead);
            }
            else
            {
                EZP_IBC_U::debug("email is empty so not attempting lifecycle transition. event type $event_type");
            }

            return self::convert_to_wpcontact($contact);
        }
        /*
         * Safe copy all fields from source to destination, assign source contact events to destination, delete the source
         */

        private static function combine_contacts($source_contact, $dest_contact)
        {
            if ($source_contact->id == $dest_contact->id)
            {
                EZP_IBC_U::debug("ERROR. Attempting to merge contact $source_contact->id into itself!");
            }

            /* @var $source_contact EZP_IBC_Contact_Entity */
            /* @var $dest_contact EZP_IBC_Contact_Entity */
            EZP_IBC_U::debug("Merging contact $source_contact->id into $dest_contact->id");

            // Copy any data over, save
            self::safe_copy_field($source_contact, $dest_contact, 'address');
            self::safe_copy_field($source_contact, $dest_contact, 'city');
            self::safe_copy_field($source_contact, $dest_contact, 'country_code');
            self::safe_copy_field($source_contact, $dest_contact, 'email');
            self::safe_copy_field($source_contact, $dest_contact, 'first_name');
            self::safe_copy_field($source_contact, $dest_contact, 'last_name');
            self::safe_copy_field($source_contact, $dest_contact, 'referral_path');

            $source_list_ids = $source_contact->get_list_ids();
            foreach ($source_list_ids as $source_list_id)
            {
                $dest_contact->add_to_list($source_list_id);
            }

            self::safe_copy_field($source_contact, $dest_contact, 'notes');
            self::safe_copy_field($source_contact, $dest_contact, 'phone');
            self::safe_copy_field($source_contact, $dest_contact, 'state_region');

            if (($source_contact->wpid != -1) && ($dest_contact->wpid == -1))
            {
                $dest_contact->wpid = $source_contact->wpid;
            }

            if ($source_contact->stage > $dest_contact->stage)
            {
                $dest_contact->stage = $source_contact->stage;
            }

            self::safe_copy_field($source_contact, $dest_contact, 'zip_code');

            $dest_contact->save();

            // ** Reassign public ids from source to dest **/
            $public_ids = EZP_IBC_Public_ID_Entity::get_all_by_contact_id($source_contact->id);

            foreach ($public_ids as $public_id)
            {
                /* @var $public_id as EZP_IBC_Public_ID_Entity */
                $public_id->contact_id = $dest_contact->id;
                $public_id->save();
            }

            // ** Reassign events from source to dest **
            $events = EZP_IBC_Event_Entity::get_all_by_contact_id($source_contact->id);

            foreach ($events as $event)
            {
                /* @var $event EZP_IBC_Event_Entity */
                $event->contact_id = $dest_contact->id;
                $event->save();
            }

            $source_contact->delete();
        }

        private static function safe_copy_field($source_contact, $dest_contact, $field_name)
        {
            if ($source_contact->$field_name != '')
            {
                $dest_contact->$field_name = $source_contact->$field_name;
            }
        }

        private static function get_contact_by_data($contact_data)
        {
            $contact = null;

            if ($contact_data->email != '')
            {
                EZP_IBC_U::debug("Looking up contact by email $contact_data->email");
                // See if contact exists under the email - meaning this is an alias
                $contact = EZP_IBC_Contact_Entity::get_by_email_address($contact_data->email);
            }

            if ($contact == null)
            {
                if ($contact_data->wpid != -1)
                {
                    EZP_IBC_U::debug("Looking up contact by wpid $contact_data->wpid");
                    $contact = EZP_IBC_Contact_Entity::get_by_wpid($contact_data->wpid);

                    if ($contact == null)
                    {
                        /* Didn't get it by the wpid so try one more time by the email address of the wordpress user */
                        $user = get_userdata($contact_data->wpid);

                        if ($user != false)
                        {
                            $contact = EZP_IBC_Contact_Entity::get_by_email_address($user->user_email);
                        }
                    }
                }
            }

            return $contact;
        }

        private static function set_contact_values($contact_data, $contact)
        {
            $changed = false;

            if ($contact_data != null)
            {
                $changed |= self::set_contact_field_value($contact_data, $contact, 'email');
                $changed |= self::set_contact_field_value($contact_data, $contact, 'first_name');
                $changed |= self::set_contact_field_value($contact_data, $contact, 'last_name');
                $changed |= self::set_contact_field_value($contact_data, $contact, 'wpid');
            }

            return $changed;
        }

        private static function set_contact_field_value($contact_data, $contact, $field_name)
        {
            $changed = false;
            $should_set = false;

            if (is_numeric($contact_data->$field_name))
            {
                $should_set = (($contact_data->$field_name != -1) && ($contact_data->$field_name != $contact->$field_name));
            }
            else
            {
                $should_set = (($contact_data->$field_name != '') && ($contact_data->$field_name != $contact->$field_name));
            }

            if ($should_set)
            {
                EZP_IBC_U::debug("setting $field_name of contact");
                $contact->$field_name = $contact_data->$field_name;
                $changed = true;
            }

            return $changed;
        }

        private static function populate_form_contact_data($form_contact, $form_data)
        {
            /* @var $form_contact EZP_IBC_Contact_Entity */

            $form_contact->email = '';
            $form_contact->first_name = '';
            $form_contact->last_name = '';


            foreach ($form_data as $datum)
            {
                $label = strtolower($datum['label']);
                $value = $datum['value'];

                if (empty($label))
                {
                    $label = $datum['placeholder'];
                }

                if (filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $form_contact->email = $value;
                }
                else if ($label == 'name' || EZP_IBC_U::starts_with($label, 'name'))
                {
                    $name_pieces = explode(' ', $value);

                    if (is_array($name_pieces) && (count($name_pieces) == 2))
                    {
                        $form_contact->first_name = $name_pieces[0];
                        $form_contact->last_name = $name_pieces[1];
                    }
                    else
                    {
                        $form_contact->first_name = $value;
                    }
                }
                else if ($label == 'first' || EZP_IBC_U::starts_with($label, 'first name'))
                {
                    $form_contact->first_name = $value;
                }
                else if ($label == 'last' || EZP_IBC_U::starts_with($label, 'last name'))
                {
                    $form_contact->last_name = $value;
                }
//                else if (strpos($label, 'phone') !== false)
//                {
//                    $form_contact->phone = $value;
//                }
//                else if (strpos($label, 'address') !== false)
//                {
//                    $form_contact->address = $value;
//                }
//                else if (strpos($label, 'city') !== false)
//                {
//                    $form_contact->city = $value;
//                }
//                else if ((strpos($label, 'state') !== false) || strpos($label, 'region') !== false)
//                {
//                    $form_contact->state_region = $value;
//                }
//                else if (strpos($label, 'zip') !== false)
//                {
//                    $form_contact->zip_code = $value;
//                }
//                else if (strpos($label, 'country') !== false)
//                {
//                    $form_contact->country_code = $value;
//                }
            }
        }

        // Update membership in all contacts
        public static function update_list_membership($list_id, $member_ids)
        {
            $contacts = self::get_all();

            foreach ($contacts as $contact)
            {
                /* @var $contact EZP_IBC_Contact_Entity */
                $should_be_member = in_array($contact->id, $member_ids);

                //EZP_IBC_U::ddebug(gettype($list_id));
                $current_list_ids = $contact->get_list_ids();

                $currently_a_member = in_array($list_id, $current_list_ids);
                EZP_IBC_U::ddebug($current_list_ids);

                if ($should_be_member)
                {
                    //    EZP_IBC_U::ddebug("$contact->id should be a member of $list_id");
                    if ($currently_a_member == false)
                    {
                        EZP_IBC_U::ddebug("adding");
                        $contact->add_to_list($list_id);
                        $contact->save();
                    }
                }
                else
                {
                    if ($currently_a_member == true)
                    {
                        //      EZP_IBC_U::ddebug("removing $contact->id from $list_id");
                        $contact->remove_from_list($list_id);
                        $contact->save();
                    }
                }
            }
        }

        // RSR TODO: include user field mappings
//        public static function get_field_display_name_mapping()
//        {
//            $mapping = array();
//
//            $mapping[EZP_IBC_Field_Ids::Last_Name] = EZP_IBC_U::__('Last Name');
//            $mapping[EZP_IBC_Field_Ids::First_Name] = EZP_IBC_U::__('First Name');
//            $mapping[EZP_IBC_Field_Ids::Email] = EZP_IBC_U::__('Email');
//            $mapping[EZP_IBC_Field_Ids::Phone] = EZP_IBC_U::__('Phone');
//            $mapping[EZP_IBC_Field_Ids::Address] = EZP_IBC_U::__('Address');
//            $mapping[EZP_IBC_Field_Ids::City] = EZP_IBC_U::__('City');
//            $mapping[EZP_IBC_Field_Ids::State_Region] = EZP_IBC_U::__('State/Region');
//            $mapping[EZP_IBC_Field_Ids::Zip_Code] = EZP_IBC_U::__('Zip Code');
//            $mapping[EZP_IBC_Field_Ids::Notes] = EZP_IBC_U::__('Notes');
//            $mapping[EZP_IBC_Field_Ids::Type] = EZP_IBC_U::__('Type');
//            $mapping[EZP_IBC_Field_Ids::Creation_Timestamp] = EZP_IBC_U::__('Creation Timestamp');
//            $mapping[EZP_IBC_Field_Ids::Last_Event_Timestamp] = EZP_IBC_U::__('Last Event Timestamp');
//            $mapping[EZP_IBC_Field_Ids::Last_Browser] = EZP_IBC_U::__('Last Browser');
//            $mapping[EZP_IBC_Field_Ids::Last_IP] = EZP_IBC_U::__('Last IP');
//            
//            return $mapping;
//        }
//        
//        public static function get_field_display_name($field_id)
//        {
//            $mapping = self::get_field_display_name_mapping();
//            
//            if(array_key_exists($field_id, $mapping))
//            {
//                return $mapping[$field_id];
//            }
//            else
//            {
//                return EZP_IBC_U::__('Unknown');
//            }
//        }
//        public static function get_field_name_mapping()
//        {
//            $mapping = array();
//            
//            $mapping[EZP_IBC_Field_Ids::Last_Name] = EZP_IBC_U::__('last_name');
//            $mapping[EZP_IBC_Field_Ids::First_Name] = EZP_IBC_U::__('first_name');
//            $mapping[EZP_IBC_Field_Ids::Email] = EZP_IBC_U::__('email');
//            $mapping[EZP_IBC_Field_Ids::Phone] = EZP_IBC_U::__('phone');
//            $mapping[EZP_IBC_Field_Ids::Address] = EZP_IBC_U::__('address');
//            $mapping[EZP_IBC_Field_Ids::City] = EZP_IBC_U::__('city');
//            $mapping[EZP_IBC_Field_Ids::State_Region] = EZP_IBC_U::__('state_region');
//            $mapping[EZP_IBC_Field_Ids::Zip_Code] = EZP_IBC_U::__('zip_code');
//            $mapping[EZP_IBC_Field_Ids::Notes] = EZP_IBC_U::__('notes');
//            $mapping[EZP_IBC_Field_Ids::Type] = EZP_IBC_U::__('type');
//            $mapping[EZP_IBC_Field_Ids::Creation_Timestamp] = EZP_IBC_U::__('creation_timestamp');
//            $mapping[EZP_IBC_Field_Ids::Last_Event_Timestamp] = EZP_IBC_U::__('last_event_timestamp');
//            $mapping[EZP_IBC_Field_Ids::Last_Browser] = EZP_IBC_U::__('last_browser');
//            $mapping[EZP_IBC_Field_Ids::Last_IP] = EZP_IBC_U::__('last_ip');
//            
//            return $mapping;
//        }
//        public static function get_field_name($field_id)
//        {
//            $mapping = self::get_field_name_mapping();
//            
//            if(array_key_exists($field_id, $mapping))
//            {
//                return $mapping[$field_id];
//            }
//            else
//            {
//                return null;
//            }            
//        }

        public function delete()
        {
            self::delete_by_id($this->id);
        }

        public static function delete_all()
        {
            parent::delete_all_base(self::$TABLE_NAME);
        }

        public static function delete_by_id($id)
        {
            EZP_IBC_Public_ID_Entity::delete_by_contact_id($id);

            EZP_IBC_Event_Entity::delete_by_contact_id($id);

            self::delete_by_id_and_table($id, self::$TABLE_NAME);
        }

        public static function get_all($filter = '', $convert_to_wp = true)
        {
            // Convert the filter into the equivalent sql where clause
            // name = *x* => like %x%
            // '' => no where
            // ...todo

            $where_text = '';

            if (strpos($filter, '*') !== false)
            {

                $where_text = 'where ' . str_replace('*', '%', $filter);
            }

            $contacts = self::get_all_objects(get_class(), self::$TABLE_NAME, $where_text);

            if ($convert_to_wp)
            {
                foreach ($contacts as $contact)
                {
                    self::convert_to_wpcontact($contact);
                }
            }

            return $contacts;
        }

        public static function get_all_by_custom_query($query_string)
        {
            return self::get_all_objects_by_custom_query(get_class(), self::$TABLE_NAME, $query_string);
        }

        // Retrieve all contacts that have events matching the where clause
        public static function get_by_contact_event_where($contact_where_clause = null, $event_where_clause = null)
        {
            global $wpdb;

            if ($event_where_clause == null)
            {
                return self::get_all_where($contact_where_clause);
            }

            $contact_table_name = $wpdb->prefix . EZP_IBC_Contact_Entity::$TABLE_NAME;

            $event_table_name = $wpdb->prefix . EZP_IBC_Event_Entity::$TABLE_NAME;

            $contact_where_string = '';
            $event_where_string = '';

            if ($contact_where_clause != null)
            {
                $contact_where_string = "where $contact_where_clause";
            }

            if ($event_where_clause != null)
            {
                $event_where_string = "where $event_where_clause";
            }

            $query_string = "select * from $contact_table_name inner join (select distinct contact_id from $event_table_name $event_where_string) as event_contacts on id = event_contacts.contact_id $contact_where_string;";

//            if(strpos($query_string, 'ninja') !== false)
//            {
//                EZP_IBC_U::debug($query_string);
//            }
            //     echo "$query_string";
            $rows = $wpdb->get_results($query_string);

            $contacts = array();

            foreach ($rows as $row)
            {
                $contact = self::get_instance_from_row($row, get_class(), $contact_table_name);

                self::convert_to_wpcontact($contact);

                array_push($contacts, $contact);
            }

            return $contacts;
        }

        public static function get_all_by_contact_ids($contact_ids, $where_clause = '')
        {
            if ($where_clause != '')
            {
                $where_clause = "($where_clause) AND ";
            }

            $where_clause .= 'id in (';
            $where_clause .= implode(',', $contact_ids);
            $where_clause .= ')';

            return self::get_all_where($where_clause);
        }

        public static function get_all_by_stage($stage)
        {
            return self::get_all_where("stage = $stage");
        }

        public static function get_all_where($where_clause = '', $order_by_clause = '')
        {
            $where_text = empty($where_clause) ? '' : "WHERE $where_clause";
            $order_by_text = empty($order_by_clause) ? '' : "ORDER BY $order_by_clause";

            $contacts = self::get_all_objects(get_class(), self::$TABLE_NAME, $where_text, $order_by_text);

            foreach ($contacts as $contact)
            {
                self::convert_to_wpcontact($contact);
            }

            return $contacts;
        }

        /**
         * @return EZP_IBC_Contact_Entity
         */
        public static function get_by_id($id)
        {
            $contact = self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);

            return self::convert_to_wpcontact($contact);
        }

        public static function get_all_by_list_id($list_id)
        {
            $members = array();
            $contacts = self::get_all();

            foreach ($contacts as $contact)
            {
                /* @var $contact EZP_IBC_Contact_Entity */
                $list_ids = $contact->get_list_ids();

                $is_member = in_array($list_id, $list_ids);

                if ($is_member)
                {
                    array_push($members, $contact);
                }
            }

            return $members;
        }

        /**
         * @return EZP_Contact_Entity
         */
        public static function get_by_cookie($cookie, $referrer)
        {
            $contact = null;
            $pid = EZP_IBC_U::get_pid_from_cookie($cookie);

            if ($pid != null)
            {
                $contact = self::get_by_pid($pid, $referrer);
            }

            return $contact;
        }

        /**
         * @return EZP_Contact_Entity
         */
        public static function get_by_pid($pid, $referrer)
        {
            $contact = null;
            $public_id = EZP_IBC_Public_ID_Entity::get_by_pid($pid);

            if ($public_id != null)
            {
                $contact = self::get_by_id($public_id->contact_id);

                EZP_IBC_U::debug("Found contact $public_id->contact_id for $pid");
            }
            else
            {
                // Cookie exists but PID doesn't - could indicate a multisite install has already created this PID so leave it alone and just create a PID in this install

                $contact = new EZP_IBC_Contact_Entity();

                $contact->set_referer($referrer);
                $contact->save();

                $public_id = new EZP_IBC_Public_ID_Entity();

                $public_id->contact_id = $contact->id;
                $public_id->pid = $pid;

                $public_id->save();

                EZP_IBC_U::debug("Couldn't find entity for the cookid PID ($pid) so creating one. Multisite install?");
            }

            return self::convert_to_wpcontact($contact);
        }

        /**
         * @return EZP_Contact_Entity
         */
        public static function get_by_email_address($email_address)
        {
            $contact = self::get_by_unique_field_and_type('email', $email_address, get_class(), self::$TABLE_NAME);

            if ($contact == null)
            {
                // Search through WordPress IDs then try to match up the equivalent contact
                $wp_users = get_users(array('search' => $email_address));

                foreach ($wp_users as $wp_user)
                {
                    EZP_IBC_U::debug("cycling thru wp users ($wp_user->ID)");

                    if ($wp_user->user_email == $email_address)
                    {
                        EZP_IBC_U::debug("Found wp user with email $email_address");
                        $contact = self::get_by_wpid($wp_user->ID);
                        break;
                    }
                }
            }
            return self::convert_to_wpcontact($contact);
        }

        /**
         * @return EZP_Contact_Entity
         */
        public static function get_by_wpid($wpid)
        {
            $contact = self::get_by_unique_field_and_type('wpid', $wpid, get_class(), self::$TABLE_NAME);

            return self::convert_to_wpcontact($contact);
        }

        // Allow WordPress fields to override previous values if the contact is tied to a WordPress ID
        private static function convert_to_wpcontact($contact)
        {
            if ($contact != null)
            {
                /* @var $contact EZP_IBC_Contact_Entity */

                if ($contact->wpid != -1)
                {
                    $user = get_userdata($contact->wpid);

                    if ($user != false)
                    {
                        $contact->email = "$user->user_email";

                        $contact->_wp_login = $user->user_login;
                    }
                }
            }

            return $contact;
        }
    }
}
?>