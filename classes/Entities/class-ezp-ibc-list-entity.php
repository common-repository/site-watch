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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-standard-entity-base.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-contact-entity.php');

if (!class_exists('EZP_IBC_List_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_List_Entity extends EZP_IBC_JSON_Entity_Base
    {
        public $name = '';

        function __construct()
        {
            parent::__construct();
            
            $this->verifiers['name'] = new EZP_IBC_Required_Verifier("Name must not be blank");
        }
        
        public static function get_all()
        {
            return self::get_by_type(get_class());
        }
        
        public static function delete_by_id($list_id)
        {
            $contacts = EZP_IBC_Contact_Entity::get_all_by_list_id($list_id);
            
            foreach($contacts as $contact)
            {
                /* @var $contact EZP_IBC_Contact_Entity */
                $contact->remove_from_list($list_id);
                
                $contact->save();               
            }
            
            parent::delete_by_id_base($list_id);
        }
        
        public function set_post_variables($post) {
        
//            $member_ids = json_decode($post['contact_ids']);
                                 
  //          EZP_IBC_Contact_Entity::update_list_membership($this->id, $member_ids);            
            
            return parent::set_post_variables($post);
        }
        
        public function get_member_count()
        {
            $member_count = 0;
            $contacts = EZP_IBC_Contact_Entity::get_all_where("list_ids <> ''");
            
            foreach($contacts as $contact)
            {
                /* @var $contact EZP_IBC_Contact_Entity */
                $list_ids = $contact->get_list_ids();
                
                if(in_array($this->id, $list_ids))
                {
                    $member_count++;
                }
            }
            
            return $member_count;
        }

        /**
         * 
         * @param type $id
         * @return EZP_IBC_List_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class());
        }
    }           
}
?>