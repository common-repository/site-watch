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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Utilities/class-ezp-ibc-u.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-standard-entity-base.php');

if (!class_exists('EZP_IBC_ECommerce_Modes'))
{

    abstract class EZP_IBC_ECommerce_Modes
    {
        const NONE = 0;
        const WOO_COMMERCE = 1;
    }       
}

if (!class_exists('EZP_IBC_Form_Capture_Modes'))
{

    abstract class EZP_IBC_Form_Capture_Modes
    {
        const Capture_All_Except_List = 0;
        const Capture_Only_List = 1;        
    }       
}

if (!class_exists('EZP_IBC_Ajax_Protocols'))
{

    abstract class EZP_IBC_Ajax_Protocols
    {
        const Auto = 0;
        const Http = 1;        
        const Https = 2;
    }       
}

if (!class_exists('EZP_IBC_Score_Range'))
{

    abstract class EZP_IBC_Score_Range
    {
        const Min = -100;
        const Max = 100;        
    }       
}
        
if (!class_exists('EZP_IBC_Global_Entity')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Global_Entity extends EZP_IBC_JSON_Entity_Base {

        public $ecommerce_mode = EZP_IBC_ECommerce_Modes::NONE;
        public $form_capture_mode = EZP_IBC_Form_Capture_Modes::Capture_All_Except_List;
        public $form_capture_list = '';
        //public $track_logged_in_users = 0;        
        
        public $storage_pp_max_inactive_prospect_age_in_days = 30;
        public $storage_pp_page_history_retention_in_days = 30;
        public $storage_pp_max_pages_per_visitor = 100;
        
        public $storage_alerts_wordpress_db_size_in_MB = 20;
        public $storage_alerts_ibc_table_size_in_MB = 5; 
        
        public $drop_tables_on_uninstall = false;
        
        public $start_here_enabled = true;
        
        public $ajax_protocol = EZP_IBC_Ajax_Protocols::Auto;
        
        public $page_view_event_worth = 1;
        public $form_submit_event_worth = 20;
        public $login_event_worth = 0;
        public $purchase_event_worth = 70;
        
        public function __construct()
        {
            parent::__construct();
            
            $this->verifiers['form_capture_list'] = new EZP_IBC_Length_Verifier(255, __("Form list can't be lo181nger than 255 characters"));
            
            $this->verifiers['storage_pp_max_inactive_prospect_age_in_days'] = new EZP_IBC_Range_Verifier(0, 180, __("Max inactive property age must be between 0 and 180"));
            $this->verifiers['storage_pp_page_history_retention_in_days'] = new EZP_IBC_Range_Verifier(0, 180, __("History retention must be between 0 and 180"));
            $this->verifiers['storage_pp_max_pages_per_visitor'] = new EZP_IBC_Range_Verifier(0, 1000, __("Max pages must be between 0 and 1000"));
            
            $this->verifiers['storage_alerts_wordpress_db_size_in_MB'] = new EZP_IBC_Range_Verifier(0, 1000000, __("WordPress DB size alert must be between 0 and 1000000"));
            $this->verifiers['storage_alerts_ibc_table_size_in_MB'] = new EZP_IBC_Range_Verifier(0, 1000000, __("Max pages must be between 0 and 1000000"));

            $this->verifiers['page_view_event_worth'] = new EZP_IBC_Range_Verifier(EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max, sprintf(__('Page view event worth must be between %1$s and %2$s'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max));
            $this->verifiers['form_submit_event__worth'] = new EZP_IBC_Range_Verifier(EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max, sprintf(__('Form submission event worth must be between %1$s and %2$s'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max));
            $this->verifiers['login_event_worth'] = new EZP_IBC_Range_Verifier(EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max, sprintf(__('Login event worth must be between %1$s and %2$s'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max));
            $this->verifiers['purchase_event_worth'] = new EZP_IBC_Range_Verifier(EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max, sprintf(__('Purchase event worth must be between %1$s and %2$s'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max));
        }
        
        public static function initialize_plugin_data() {

            $globals = parent::get_by_type(get_class());
            /* @var $globals EZP_IBC_Global_Entity */
            
            if(count($globals) == 0) 
            {                
                $global = new EZP_IBC_Global_Entity();

                $global->save();
            }
        }

        /*
         * @return EZP_IBC_Global_Entity
         */
        public static function get_instance() {
            
            $global = null;
            $globals = EZP_IBC_JSON_Entity_Base::get_by_type(get_class());

            if(count($globals) > 0) 
            {                
                $global = $globals[0];
            } 
           
            return $global;
        }
        
        public function get_protocol_string()
        {
            switch($this->ajax_protocol)
            {                    
                case EZP_IBC_Ajax_Protocols::Http:
                    return 'http';
                    break;
                    
                case EZP_IBC_Ajax_Protocols::Https:
                    return 'https';
                    break;
                
                default:
                    return 'admin';
                    break;                    
            }
        }
    }
}
?>
