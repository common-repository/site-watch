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

require_once(dirname(__FILE__) . '/class-ezp-ibc-global-entity.php');
require_once(dirname(__FILE__) . '/class-ezp-ibc-list-entity.php');

if (!class_exists('EZP_IBC_Rule_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Rule_Entity extends EZP_IBC_JSON_Entity_Base
    {
        public $name;
        public $event_type;
        public $event_filter = '';
        public $compound_action = '';

        function __construct()
        {
            $this->event_type = EZP_IBC_Event_Types::Form_Submitted;

            // parent::__construct(self::$TABLE_NAME);
            parent::__construct();            
        }
        /*
         * @var @contact EZP_IBC_Contact_Entity
         */

        public static function process_form_submit_event($form_id, $post, $contact)
        {
            $form_rules = self::get_all_by_event_type(EZP_IBC_Event_Types::Form_Submitted);

            EZP_IBC_U::debug('got all form rules');
            foreach ($form_rules as $form_rule)
            {

                /* @var $form_rule EZP_IBC_Rule_Entity */

                EZP_IBC_U::debug("processing form rule $form_rule->name");

                $form_rule->execute_form_rule($form_id, $post, $contact);
            }
        }

        public function execute_form_rule($form_id, $post, $contact)
        {
            // Using event_filter, determine if should execute the rule based on form criteria

            /* Example event_filters for form rules             
             * form.id = x
             * form.id = any
             * post.{field} = y
             * $contact.{field} = x && $post.{field} = y && form.id = 3
             */
            $id_pattern = '/form.id=(?<id>.+$)/';

            if (preg_match($id_pattern, $this->event_filter, $matches) == 1)
            {

                $form_id_pattern = $matches['id'];

                EZP_IBC_U::debug("it matches id=$form_id_pattern");

                if (($form_id_pattern == $form_id) || ($form_id_pattern == 'any'))
                {
                    $this->execute_form_action($form_id, $post, $contact);
                }
            }
        }

        private function execute_form_action($form_id, $post, $contact)
        {
            /* @var $contact EZP_IBC_Contact_Entity */

            // Determine which action to take then take it
            // Can be send_email            
            $send_email_pattern = '/send_email\(\)/';
            EZP_IBC_U::debug("execute_form_action");
            if (preg_match($send_email_pattern, $this->compound_action, $matches) == 1)
            {
                $this->send_form_email($contact, $form_id);
            }
            else
            {
                // Add to list: add_to_list(list_num) where list_num can equal all
                $list_pattern = '/add_to_list\((?<id>.*)\)/';

                if (preg_match($list_pattern, $this->compound_action, $matches) == 1)
                {
                    EZP_IBC_U::debug("add to list");
                    $list_id = (int) $matches['id'];

                    EZP_IBC_U::debug("Adding $contact->email to list $list_id");

                    $list = EZP_IBC_List_Entity::get_by_id($list_id);

                    if ($list != null)
                    {
                        $contact->add_to_list($list_id);

                        $contact->save();
                    }
                    else
                    {
                        EZP_IBC_U::debug("Bad id $list_id specified in add to list action.");
                    }
                }
            }
        }

        private function send_form_email($contact, $form_id)
        {
            /* @var $contact EZP_IBC_Contact_Entity */
            $global = EZP_IBC_Global_Entity::get_instance();
            
            if (!empty($global->notify_email))
            {
                EZP_IBC_U::debug("Trying to send email indicating form $form_id was submitted.");

                $form = EZP_IBC_Form_Entity::get_by_id($form_id);
                
                $subject = EZP_IBC_U::__('Form ') . $form_id . EZP_IBC_U::__(' has been submitted by ' . $contact->email);
                $message = EZP_IBC_U::__('This is just a quick note to let you know that form ') . $form_id . EZP_IBC_U::__(' has been submitted by ' . $contact->email);

                if (wp_mail($global->notify_email, $subject, $message))
                {
                    EZP_IBC_U::debug("Email to $global->notify_email indicating form $form_id was sent successfully sent.");
                }
                else
                {
                    EZP_IBC_U::debug("Email to $global->notify_email indicating form $form_id failed to be sent.");
                }
            }
            else
            {
                $display_name = $contact->get_display_name();
                
                EZP_IBC_U::debug("Tried to send email regarding form $form_id submission by $display_name but no notification email address configured.");
            }
        }

        public static function get_all()
        {
            return self::get_by_type(get_class());
            //return self::get_all_objects(get_class(), self::$TABLE_NAME);
        }

        public static function get_all_by_event_type($event_type)
        {
            return self::get_by_type_and_field(get_class(), 'event_type', $event_type);
        }

        /**
         * 
         * @param type $id
         * @return EZP_IBC_Rule_Entity
         */
        public static function get_by_id($id)
        {
            //  return self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);
            return self::get_by_id_and_type($id, get_class());
        }
    }
}
?>