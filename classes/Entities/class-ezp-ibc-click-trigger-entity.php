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

//if (!class_exists('EZP_IBC_Click_Trigger_Types'))
//{
//    abstract class EZP_IBC_Page_Trigger_Types
//    {
//        const Page_ID = 0;
//        const Post_ID = 1;
//        const URL = 2;
//    }
//}

if (!class_exists('EZP_IBC_Click_Trigger_Entity'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Click_Trigger_Entity extends EZP_IBC_JSON_Entity_Base
    {
        public $selector = '';
        public $user_event_type_id = -1;


        public function __construct()
        {
            parent::__construct();

            $this->verifiers['selector'] = new EZP_IBC_Required_Verifier(__('Selector must not be blank'));
        }     

        public static function delete_by_user_event_type_id($user_event_type_id)
        {
            self::delete_by_type_and_field(get_class(), 'user_event_type_id', $user_event_type_id);
        }

        public static function get_all()
        {
            return self::get_by_type(get_class());
        }

        /**
         * @return EZP_IBC_Click_Trigger_Entity
         */
        public static function get_by_id($id)
        {
            return self::get_by_id_and_type($id, get_class());
        }
    }
}
?>