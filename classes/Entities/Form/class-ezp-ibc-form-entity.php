<?php //

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
//
//require_once('class-ezp-ibc-json-entity-base.php');
//require_once('class-ezp-ibc-form-field-entity.php');
//require_once('class-ezp-ibc-form-template-entity.php');
//require_once('class-ezp-ibc-rule-entity.php');
//
//require_once(dirname(__FILE__) . '/class-ezp-ibc-contact-entity.php');
//
//if (!class_exists('EZP_IBC_Form_Post_Status'))
//{
//
//    abstract class EZP_IBC_Form_Postback_Status
//    {
//        const Success = 0;
//        const Unknown_Submitter = -1;
//    }
//}
//
//if (!class_exists('EZP_IBC_Form_Entity'))
//{
//
//    /**
//     * @author Bob Riley <bob@easypiewp.com>
//     * @copyright 2015 Synthetic Thought LLC
//     */
//    class EZP_IBC_Form_Entity extends EZP_IBC_JSON_Entity_Base
//    {        
//        public $name = '';
//        public $template_id = EZP_IBC_Built_In_Form_Template_ID::OneBy;                                
//        public $spam_question = '';
//        public $spam_answer = '';        
//        public $spam_check_required = true;
//        
//        // Only apply if they don't redirect to a success page
//        public $post_success_message = '';
//        public $hide_submitted_form = true;
//        public $clear_submitted_form = true;
//        
//        // Where to go after submission has succeeded
//        public $success_url = ''; //rsr todo: ensure validator limits to 255!
//        
//        private static $postback_status = array();
//        
//        //public static $TABLE_NAME = 'easy_pie_ibc_forms';
//
//        // RSR TODO: put spam fields/chooser directly on form!? since either have 0 or 1 no sense adding a full join table
//        // -enabled, spam question, spam answer
//        function __construct()
//        {
//            $this->post_success_message = EZP_IBC_U::__('Form successfully submitted.');
//            $this->spam_question = EZP_IBC_U::__("What's 4 times two?");
//
//            parent::__construct();
//        }
//
//        public function process_user_post($post)
//        {                             
//            /*
//             * @var $contact EZP_IBC_Contact_Entity
//             */
//            
//            /*-- Retreive contact --*/
//            if(isset($post['email']))
//            {    
//                $email = $post['email'];
//            
//                EZP_IBC_U::ddebug("email found:$email");
//                $contact = EZP_IBC_Contact_Entity::get_by_email_address($email);
//                
//                if($contact == null)
//                {
//                    EZP_IBC_U::ddebug('No contact with that email so creating contact');
//                    $contact = new EZP_IBC_Contact_Entity();
//                    
//                    $contact->email = $email;
//                    $success = $contact->transition(EZP_IBC_Contact_Stages::Lead);
//                    
//                    $contact->save();                    
//                }
//                else
//                {
//                    EZP_IBC_U::ddebug('Found contact by email');
//                }
//            }
//            else
//            {           
//                // Form isn't collecting email so it has to be an already known lead
//                $contact = EZP_IBC_Contact_Entity::get_by_cookie($_COOKIE);
//                
//                if($contact == null)
//                {
//                    EZP_IBC_U::ddebug('unknown submitter');
//                    
//                    // If no cookie exists AND no email exists toss it out
//                    self::set_postback_status($this->id, EZP_IBC_Form_Postback_Status::Unknown_Submitter);
//                    
//                    return;
//                }  
//                
//                EZP_IBC_U::ddebug('Found contact by cookie-');
//            }
//            
//            /*-- Record event --*/
//            $event = new EZP_IBC_Event_Entity();
//            
//            $event->contact_id = $contact->id;
//            $event->event_group_id = -1;    //todo
//            $event->parameter = "form-$this->id";    // todo: set id to unique form identifier
//            $event->type = EZP_IBC_Event_Types::Form_Submitted;
//            $event->data = json_encode($post);  // rsr todo: post object best thing to encode?
//            
//            $event->save();
//            
//            EZP_IBC_U::debug('Saved event-');
//            
//            /*-- Proces matching rules --*/
//            EZP_IBC_Rule_Entity::process_form_submit_event($this->id, $post, $contact);
//                
//            self::set_postback_status($this->id, EZP_IBC_Form_Postback_Status::Success);
//            
//            if(empty($this->success_url))
//            {
//                // RSR TODO: need to render thank you note and/or blank out the fields etc
//                //  TODO: set a global flag indicating that they should now blank out or whatever - maybe just the form itself can determine its a successful post and render properly                
//            }
//            else
//            {
//                
//                // RSR TODO: if success_url exists wp_redirect to it otherwise hide form if setting set and/or display success instead of rendering form                
//                EZP_IBC_U::debug("tring to redirect to $this->success_url");
//          //      wp_redirect($this->success_url);
//            }
//        }
//        
//        public static function set_postback_status($form_id, $status)
//        {
//            self::$postback_status[$form_id] = $status;
//        }
//        
//        public static function get_postback_status($form_id)
//        {
//            if(array_key_exists($form_id, self::$postback_status))
//            {
//                return self::$postback_status[$form_id];
//            } 
//            else
//            {
//                return null;
//            }
//        }
//        
//        public function save_from_admin_post($post)
//        {
//            //RSR TODO: Go through this and return verifier errors and/or true/false from saved
//            $new_form = (bool) ($this->id == -1);
//
//            $error_string = $this->set_post_variables($post);
//
//            if(isset($post['form_field_id']))
//            {
//                $post_form_field_ids = $post['form_field_id'];
//            }
//            else
//            {
//                $post_form_field_ids = array();
//            }
//            
//            if ($error_string == "")
//            {
//                $saved = $this->save();
//
//                //--todo: have to ensure that verify the whole form including the dynamic fields before accepting any of it..? 
//                if ($saved == true)
//                {
//                    if ($new_form)
//                    {
//                        // Add all form fields
//                        foreach ($post_form_field_ids as $post_index => $form_field_id)
//                        {
//                            $db_form_field = new EZP_IBC_Form_Field_Entity();
//
//                            $db_form_field->save_from_post($post, $this->id, $post_index);
//                        }
//                    }
//                    else
//                    {
//                        $db_form_fields = EZP_IBC_Form_Field_Entity::get_all_by_form_id($this->id);
//
//                        foreach ($db_form_fields as $db_form_field)
//                        {
//                            /* @var $db_form_field EZP_IBC_Form_Field_Entity */
//
//                            $in_post = false;
//
//                            foreach ($post_form_field_ids as $post_index => $form_field_id)
//                            {
//                                if ($db_form_field->id == $form_field_id)
//                                {
//                                    // It's an update
//                                    $db_form_field->save_from_post($post, $this->id, $post_index);
//                                    
//                                    $in_post = true;
//
//                                    break;
//                                }
//                            }
//
//                            if (!$in_post)
//                            {
//                                // They must have deleted it
//                                $db_form_field->delete();
//                            }
//                        }
//
//                        // Now add any form fields that are new
//                        // rsr todo consolidate logic
//                        foreach ($post_form_field_ids as $post_index => $form_field_id)
//                        {
//                            if ($form_field_id == -1)
//                            {
//                                $db_form_field = new EZP_IBC_Form_Field_Entity();
//                                                                
//                                $db_form_field->save_from_post($post, $this->id, $post_index);
//                            }
//                        }
//                    }
//                }
//
//                return $saved;
//            }
//            else
//            {
//                return $error_string;
//            }
//        }
//
//        public static function delete_by_id($id)
//        {
//            EZP_IBC_Form_Field_Entity::delete_all_by_form_id($id);
//
//            $instance = self::get_by_id($id);
//            
//            $instance->delete();            
//            //self::delete_by_id_and_table($id, self::$TABLE_NAME);
//        }
//
//        public static function get_all()
//        {
//            return self::get_by_type(get_class());
//        }
//
//        
//        /**
//         * 
//         * @param type $id
//         * @return EZP_IBC_Form_Entity
//         */
//        public static function get_by_id($id)
//        {
//            return self::get_by_id_and_type($id, get_class());
//        }
//
//        public function get_form_fields()
//        {
//            $form_fields = EZP_IBC_Form_Field_Entity::get_all_by_form_id($this->id);
//
//            return $form_fields;
//        }
//
//        public function render()
//        {
//            return EZP_IBC_Form_Template_Entity::render($this->template_id, $this);
//        }
//    }
//}
?>