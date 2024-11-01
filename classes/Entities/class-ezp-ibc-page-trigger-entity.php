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

require_once(dirname(__FILE__) . '/class-ezp-ibc-user-event-type-entity.php');

if (!class_exists('EZP_IBC_Page_Trigger_Types'))
{
    abstract class EZP_IBC_Page_Trigger_Types
    {
        const Page_ID = 0;
        const Post_ID = 1;
        const URL = 2;
    }
}

if (!class_exists('EZP_IBC_Page_Trigger_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Page_Trigger_Entity extends EZP_IBC_JSON_Entity_Base
    {
        //public $post_id = -1;
        public $post_ids = array();
        public $urls = array();
        public $user_event_type_id = -1;
        public $trigger_type = EZP_IBC_Page_Trigger_Types::Page_ID;
        

        public function __construct()
        {
            parent::__construct();
            
            $this->verifiers['url'] = new EZP_IBC_Regex_Verifier('/^\//', __("URL must be relative (start with '/' not http or include domain)"), true);
        }

        public static function process_url($contact_id, $url, $ip_address)
        {
            $page_triggers = self::get_all();

            if (count($page_triggers) > 0)
            {
                $post_id = url_to_postid($url);

                foreach ($page_triggers as $page_trigger)
                {                    
                    /*
                     * @var $page_trigger EZP_IBC_Page_Trigger_Entity
                     */

                    $should_process = $page_trigger->does_url_match($url);
                    
                    if(!$should_process)
                    {
                        foreach($page_trigger->post_ids as $page_trigger_post_id)
                        {
                            if($page_trigger_post_id == $post_id)
                            {
                                $should_process = true;
                                break;
                            }
                        }
                    }
                                      
                    if ($should_process)
                    {
                        $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id($page_trigger->user_event_type_id);

                        if ($user_event_type != null)
                        {
                            $event = new EZP_IBC_Event_Entity();

                            $event->type = EZP_IBC_Event_Types::User_Event;
                            $event->contact_id = $contact_id;
                            $event->parameter = $user_event_type->id;
                            $event->ip_address = $ip_address;

                            $event->save();
                            
                            $contact = EZP_IBC_Contact_Entity::get_by_id($contact_id);
                            
                            $contact->update_last_activity_timestamp($ip_address);
                            
                            $contact->score += EZP_IBC_Event_Entity::get_worth(EZP_IBC_Event_Types::User_Event, $user_event_type->id);
                            
                           // $contact->add_to_event_count(EZP_IBC_Event_Types::User_Event, $user_event_type->id);
                            
                            $contact->save();
                        } else
                        {
                            EZP_IBC_U::debug("User event type $page_trigger->user_event_type_id doesn't exist");
                        }
                    }
                }
            }
        }
        
        private function does_url_match($candidate_url)
        {       
            $matches = false;    
            
            if(($this->trigger_type == EZP_IBC_Page_Trigger_Types::URL) && (count($this->urls) > 0))
            {                                
                foreach($this->urls as $match_url)
                {
                    //EZP_IBC_U::debug("candidate url=$candidate_url match_url=$match_url");
                    // Determine if url matches
                    // todo: strip the http(s):// off the front of each

                    $trimmed_candidate_url = $this->trim_url($candidate_url);
                    $trimmed_match_url = $this->trim_url($match_url);

                    
                    //EZP_IBC_U::debug("trimmed candidate url=$trimmed_candidate_url trimmed match_url=$trimmed_match_url");
                    
                    if($trimmed_candidate_url == $trimmed_match_url)
                    {
                      //  EZP_IBC_U::debug("it matches!!!");
                        $matches = true;
                        break;
                    }
                }                
            }  
            
       //     EZP_IBC_U::debug("matches=$matches");
            return $matches;
        }
        
        private function trim_url($url)
        {
         //   $trimmed_url = preg_replace('#^https?://#', '/', $url);
            
            $trimmed_url = parse_url($url);
            
            if($trimmed_url != false)
            {
                $trimmed_url = $trimmed_url['path'];
            }
            else
            {
                $trimmed_url = '';
            }
            
            if(EZP_IBC_U::ends_with($trimmed_url, '/'))
            {
                $trimmed_url = rtrim($trimmed_url, '/');
            }
                    
            return strtolower($trimmed_url);
        }

        public static function delete_by_user_event_type_id($user_event_type_id)
        {
            self::delete_by_type_and_field(get_class(), 'user_event_type_id', $user_event_type_id);
        }

        public static function get_all()
        {
            $records = self::get_by_type(get_class());

//            if ($include_extra_data)
//            {
//                foreach ($records as $record)
//                {
//                    /*
//                     * @var $record EZP_IBC_Page_Trigger_Entity
//                     */
//                    $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id($record->user_event_type_id);
//
//                    if($record->trigger_type == EZP_IBC_Page_Trigger_Types::URL)
//                    {
//                        $record->post_title = $record->url;
//                    }
//                    else
//                    {
//                        $record->post_title = get_the_title($record->post_id);
//                    }
//                    
//                    
//                    $record->user_event_type_name = $user_event_type->name;                    
//                }
//            }

            return $records;
        }

        /**
         * @return EZP_IBC_Page_Trigger_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class());
        }
    }
}
?>