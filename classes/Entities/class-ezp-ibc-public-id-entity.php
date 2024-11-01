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

require_once(dirname(__FILE__) .  '/class-ezp-ibc-standard-entity-base.php');

if (!class_exists('EZP_IBC_Public_ID_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Public_ID_Entity extends EZP_IBC_Standard_Entity_Base {
 
        public $contact_id = -1;
        public $pid = '';
        public $last_used_timestamp;
        
        // Distinct object references the same table as other plugins
        public static $TABLE_NAME = 'easy_pie_ibc_public_ids';
        
        function __construct() {
            
            parent::__construct(self::$TABLE_NAME);
                          
            $this->pid = EZP_IBC_U::get_guid();
            $this->last_used_timestamp = date("Y-m-d H:i:s");
        }   
        
        public static function init_table() {
            
            $field_info = array();
            
            $field_info['contact_id'] = 'int';
            $field_info['pid'] = 'varchar(50)';            
            $field_info['last_used_timestamp'] = 'datetime';         
            
            $index_array = array();
            $index_array["pid_idx"] = "pid";
           
            self::generic_init_table($field_info, self::$TABLE_NAME, $index_array);
        } 
        
//        public static function delete_by_id($id) {
//        
//            self::delete_by_id_and_table($id, self::$TABLE_NAME);
//        }
//        
//        public static function get_all()
//        {
//            return self::get_all_objects(get_class(), self::$TABLE_NAME);
//        }
////        
        
        /**
         * 
         * @param type $id
         * @return EZP_IBC_Public_ID_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class(), self::$TABLE_NAME);
        }
        
        public static function get_all_by_contact_id($contact_id) 
        {
            return self::get_all_by_field_and_type('contact_id', $contact_id, get_class(), self::$TABLE_NAME);
        }        
        /**
         * 
         * @param type $pid
         * @return EZP_IBC_Public_ID_Entity
         */
        public static function get_by_pid($pid)
        {
            return self::get_by_unique_field_and_type('pid', $pid, get_class(), self::$TABLE_NAME);
        }
        
        public static function delete_by_contact_id($contact_id)
        {
            self::delete_by_field_and_table('contact_id', $contact_id, self::$TABLE_NAME);
        }
        
        public static function delete_all()
        {
            parent::delete_all_base(self::$TABLE_NAME);
        }
        
        public function set_cookie()
        {                
            EZP_IBC_U::debug("setting cookie $this->pid, time() + 3600 * 24 * 365 * 10, '/'");
            
            $retVal = setcookie(EZP_IBC_Constants::PID_COOKIE_NAME, $this->pid, time() + 3600 * 24 * 365 * 10, '/');
            
            EZP_IBC_U::debug("created cookie $this->pid (return value $retVal)");
            
            if($retVal != true)
            {
                EZP_IBC_U::debug("Error setting cookie");
            }
        }             
        
        public function save()
        {
            parent::save();
            
            $this->set_cookie();
        }
        
//        public static function get_all_by_contact_id($contact_id) 
//        {
//            return self::get_all_by_field_and_type('contact_id', $contact_id, get_class(), self::$TABLE_NAME);
//        }
//        
//        public static function delete_by_contact_id($contact_id)
//        {
//            self::delete_by_field_and_table('contact_id', $contact_id, self::$TABLE_NAME);
//        }
    }
    
 //   EZP_Contact_Entity::init_class();
}
?>