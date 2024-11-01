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
if (!class_exists('EZP_IBC_Constants')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Constants {

        //const COMPOUND_OPTION_NAME = 'easy-pie-cs-options';
        //const MAIN_PAGE_KEY = 'easy-pie-cs-main-page';
        const PLUGIN_SLUG = 'site-watch';
        const PLUGIN_VERSION = "0.5.3"; // RSR Version
        const MAIN_OPTION_KEY = 'easy-pie-ibc-options';
        
        const PID_COOKIE_NAME = 'ezp-ibc-pid';
        
        
        
        
//
//        /* Pseudo constants */
//        public static $PLUGIN_DIR;
        public static $PEOPLE_SUBMENU_SLUG;
        public static $ASSETS_SUBMENU_SLUG;
        public static $INTELLIGENCE_SUBMENU_SLUG;
        public static $DASHBOARD_SUBMENU_SLUG;
        //public static $LISTS_SUBMENU_SLUG;
        public static $SETTINGS_SUBMENU_SLUG;
        public static $SUPPORT_SUBMENU_SLUG;
        
        public static $CONTACT_SUBMENU_SLUG;        
        public static $FORM_SUBMENU_SLUG;
        public static $LIST_SUBMENU_SLUG;
        
        /* User events tab control */
        public static $PAGE_TRIGGER_SLUG;
        public static $CLICK_TRIGGER_SLUG;
        public static $USER_EVENT_TYPE_SLUG;

       
        public static function init() 
        {

            self::$DASHBOARD_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG;            
            self::$PEOPLE_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-people';
            self::$ASSETS_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-assets';
            self::$INTELLIGENCE_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-intelligence';                        
            //self::$LISTS_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-lists';            
            self::$SETTINGS_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-settings';            
            self::$SUPPORT_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-support';
            
            // Individual edit/view pages
            self::$CONTACT_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-contact';
            self::$FORM_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-form';

            self::$LIST_SUBMENU_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-list';
            
            self::$PAGE_TRIGGER_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-page-trigger';
            self::$CLICK_TRIGGER_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-click-trigger';
            self::$USER_EVENT_TYPE_SLUG = EZP_IBC_Constants::PLUGIN_SLUG . '-user-event-type';
            
        }

    }

    EZP_IBC_Constants::init();
}
?>