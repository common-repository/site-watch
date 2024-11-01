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

require_once(dirname(__FILE__) . '/class-ezp-ibc-json-entity-base.php');
require_once(dirname(__FILE__) . '/class-ezp-ibc-page-trigger-entity.php');

if (!class_exists('EZP_IBC_User_Event_Type_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_User_Event_Type_Entity extends EZP_IBC_JSON_Entity_Base
    {
        public $name = '';
        public $description = '';
        public $worth = 0;

        public function __construct()
        {
            parent::__construct();
            
            $this->verifiers['name'] = new EZP_IBC_Required_Verifier(__('Name is required'));            
            $this->verifiers['worth'] = new EZP_IBC_Range_Verifier(EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max, sprintf(__('Worth  must be between %1$s and %2$s'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max));            
        }
        
        public static function delete_by_id($id)
        {
            /* Purge all connected page and click triggers */            
            EZP_IBC_Page_Trigger_Entity::delete_by_user_event_type_id($id);            
            EZP_IBC_Click_Trigger_Entity::delete_by_user_event_type_id($id);
            
            // Just purge the events associated with th is event type - UI should give sufficient warning
            EZP_IBC_Event_Entity::delete_by_user_event_type_id($id);            
            
            parent::delete_by_id_base($id);
        }
        
        public static function get_all()
        {
            return self::get_by_type(get_class());
        }
        
        /**
         * @returns EZP_IBC_User_Event_Type_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class());
        }
    }
}
?>