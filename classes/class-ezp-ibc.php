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

require_once('class-ezp-ibc-plugin-base.php');
require_once('class-ezp-ibc-constants.php');
require_once('class-ezp-ibc-ws.php');

require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-event-entity.php");
require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-contact-entity.php");
require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-event-group-entity.php");
require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-public-id-entity.php");

require_once(dirname(__FILE__) . '/Utilities/class-ezp-ibc-options-u.php');

require_once(dirname(__FILE__) . '/class-ezp-ibc-ecommerce.php');
require_once(dirname(__FILE__) . '/class-ezp-ibc-task-scheduler.php');

require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-global-entity.php");
require_once(dirname(__FILE__) . "/Entities/class-ezp-ibc-click-trigger-entity.php");

if (!class_exists('EZP_IBC'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC extends EZP_IBC_Plugin_Base
    {
        // Variables        
        private $options;

        /**
         * Constructor
         */
        function __construct($plugin_file_path)
        {
            parent::__construct(EZP_IBC_Constants::PLUGIN_SLUG);

            $this->upgrade_system();

            $ezp_ibc_ws = new EZP_IBC_WS();

            $ezp_ibc_ws->Init();

            $this->add_class_action('wp_head', 'head_handler');
            $this->add_class_action('plugins_loaded', 'plugins_loaded_handler');

            // Boo - have to register post types on every page load!

            $this->add_class_action('init', 'init_handler');



            if (is_admin())
            {
                // rsr todo: when want to update the columns for cpt        $this->add_class_action('manage_ezp_contact_posts_columns', 'add_ezp_contact_columns');
                //EZP_IBC_U::debug('enqueueing admin scripts');
                $this->add_class_action('admin_enqueue_scripts', 'enqueue_admin_scripts_and_styles');
            }
            else
            {
                $this->add_class_action('wp_login', 'handle_wp_login', 10, 2);
                $this->add_class_action('wp_enqueue_scripts', 'enqueue_user_scripts_and_styles');
            }

            $ecommerce = EZP_IBC_ECommerce_Factory::get_ecommerce();

            if ($ecommerce != null)
            {               
                $callback = new EZP_IBC_ECommerce_Callback();

                $callback->purchase = array($this, 'handle_ecommerce_purchase');

                $ecommerce->register_callback($callback);
            }

            //- Hook Handlers
            register_activation_hook($plugin_file_path, array('EZP_IBC', 'activate'));
            register_deactivation_hook($plugin_file_path, array('EZP_IBC', 'deactivate'));

            //- Actions
            $this->add_class_action('admin_init', 'admin_init_handler');
            $this->add_class_action('admin_menu', 'add_menu_pages');

            //    $this->debug('adding ws track event');                       
        }

        function head_handler()
        {
            
        }

        public function admin_init_handler()
        {
            $this->add_filters_and_actions();
        }

        private function add_filters_and_actions()
        {

            add_filter('plugin_action_links', array($this, 'get_action_links'), 10, 2);
        }

        function get_action_links($links, $file)
        {

            if ($file == 'site-watch/site-watch.php')
            {

                //   $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . EZP_CS_Constants::PLUGIN_SLUG . '">Settings</a>';
                $url = menu_page_url(EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG, false);
                //   EZP_IBC_U::debug_object('url', $url);
                $settings_link = sprintf(__('<a href="%1$s">Settings</a>'), $url);

                EZP_IBC_U::debug("settings link $settings_link");
                array_unshift($links, $settings_link);
            }

            return $links;
        }

        function handle_ecommerce_purchase($order_line_items)
        {
            EZP_IBC_U::debug("Purchase event!");
            $contact = EZP_IBC_Contact_Entity::prep_contact_for_event($_SERVER, $_COOKIE, EZP_IBC_Event_Types::Purchase, null);

            if ($contact != null)
            {
                $contact->transition(EZP_IBC_Contact_Stages::Customer);

                foreach ($order_line_items as $line_item)
                {
                    /* @var $line_item EZP_IBC_Order_Line_Item */

                    $ip_address = $_SERVER['REMOTE_ADDR'];

                    $data = $line_item;

                    $event = new EZP_IBC_Event_Entity();
                    $event->type = EZP_IBC_Event_Types::Purchase;
                    $event->contact_id = $contact->id;
                    $event->ip_address = $ip_address;
                    $event->parameter = $line_item->order_id;
                    $event->parameter2 = $line_item->product_detail->id;
                    $event->parameter3 = $line_item->ecommerce_system_type;
                    $event->data = json_encode($data);

                    $event->save();

                    $contact->update_last_activity_timestamp($ip_address);

                    $contact->score += EZP_IBC_Event_Entity::get_worth(EZP_IBC_Event_Types::Purchase, $event->parameter, $event->parameter2, $event->parameter3);
                    
                    $contact->save();

                    $display_name = $contact->get_display_name();

                    EZP_IBC_U::debug("Saved new event for $display_name($contact->id)");
                }
            }
            else
            {
                EZP_IBC_U::debug("Not recording purchase event because couldn't create/lookup contact");
            }
        }

        function handle_wp_login($user_login, $user)
        {
            EZP_IBC_U::debug("Login detected for $user_login($user->ID)");

            $contact = EZP_IBC_Contact_Entity::prep_contact_for_event($_SERVER, $_COOKIE, EZP_IBC_Event_Types::WP_Login, $user->ID);

            if ($contact != null)
            {
                EZP_IBC_U::debug("Contact not null so saving WP login event for $user_login($user->ID)");

                $ip_address = $_SERVER['REMOTE_ADDR'];

                $event = new EZP_IBC_Event_Entity();
                $event->type = EZP_IBC_Event_Types::WP_Login;
                $event->contact_id = $contact->id;
                $event->ip_address = $ip_address;
                $event->parameter = $user->ID;

                $event->save();

                $contact->update_last_activity_timestamp($ip_address);

                $contact->score += EZP_IBC_Event_Entity::get_worth(EZP_IBC_Event_Types::WP_Login);
                
                $contact->save();

                $display_name = $contact->get_display_name();

                EZP_IBC_U::debug("Saved new event for $display_name($contact->id)");
            }
            else
            {
                EZP_IBC_U::debug("Not recording login event because couldn't create/lookup contact");
            }
        }

        // Add the contact CPT columns
        function add_ezp_contact_columns($columns)
        {
            // RSR TODO: add extra column names in here

            return $columns;
        }

        function prfx_meta_callback($post)
        {
            echo 'This is the event log';
        }

        function enqueue_admin_scripts_and_styles()
        {
            $jsRoot = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG . "/js";

            $script_path = $jsRoot . '/ezp_ibc.js';

            wp_enqueue_script('ezp_ibc', $script_path, array(), EZP_IBC_Constants::PLUGIN_VERSION);

            $font_awesome_directory = EZP_IBC_U::$PLUGIN_URL . "/font-awesome-4.2.0/css/";
            wp_register_style('fontawesome', $font_awesome_directory . 'font-awesome.min.css', array(), '4.2.0');
            wp_enqueue_style('fontawesome');
        }

        function enqueue_user_scripts_and_styles()
        {
            wp_enqueue_script('jquery');

            $jsRoot = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG . "/js";

            wp_enqueue_script('ezp_ibc.js', $jsRoot . '/ezp_ibc.js', array(), EZP_IBC_Constants::PLUGIN_VERSION);


            wp_register_script('ezp_ibc_trk', $jsRoot . '/ezp_ibc_trk.js', array('jquery'), EZP_IBC_Constants::PLUGIN_VERSION);

            $click_triggers = EZP_IBC_Click_Trigger_Entity::get_all();
            $trimmed_click_triggers = array();

            foreach ($click_triggers as $click_trigger)
            {
                $trimmed_click_trigger = array();

                $trimmed_click_trigger['selector'] = $click_trigger->selector;
                $trimmed_click_trigger['user_event_type_id'] = $click_trigger->user_event_type_id;

                array_push($trimmed_click_triggers, $trimmed_click_trigger);
            }

            $global = EZP_IBC_Global_Entity::get_instance();

            /* @var $global EZP_IBC_Global_Entity */

            $protocol = $global->get_protocol_string();

            $gateway = array('ajaxurl' => admin_url('admin-ajax.php', $protocol),
                'nonce' => wp_create_nonce('ibc_trk'),
                'form_capture_mode' => $global->form_capture_mode,
                'form_capture_list' => $global->form_capture_list,
                'click_triggers' => $trimmed_click_triggers);

            wp_localize_script('ezp_ibc_trk', 'ezp_ibc_gateway', $gateway);

            wp_enqueue_script('ezp_ibc_trk');




            $styleRoot = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG . "/styles";

            wp_register_style('easy-pie-ibc-styles.css', $styleRoot . '/easy-pie-ibc-styles.css', array(), EZP_IBC_Constants::PLUGIN_VERSION);
            wp_enqueue_style('easy-pie-ibc-styles.css');
        }

        function init_handler()
        {
     //       $this->upgrade_system();

            add_shortcode('ezp-form', array($this, "render_form_shortcode"));

            if (!is_admin())
            {
                if (isset($_POST['ezp_form_id']))
                {
                    $form = EZP_IBC_Form_Entity::get_by_id($_POST['ezp_form_id']);

                    $form->process_user_post($_POST);
                }
                //    $this->enqueue_user_scripts();
                //     $this->record_url_event();
            }

            $task_scheduler = new EZP_IBC_Task_Scheduler();

            $task_scheduler->register_actions();
        }

        private function upgrade_system($re_init = false)
        {
            //   $options = EZP_IBC_Options_U::get_all_options();
            //   $installed_ver = EZP_IBC_Options_U::get_cached_option($options, EZP_IBC_Option_Subkeys::Plugin_Version, '0.0');

            $installed_ver = EZP_IBC_Options_U::get_option(EZP_IBC_Option_Subkeys::Plugin_Version, '0.0');
            // RSR TODO: move table creation/upgrade logic out of activate

            $force_upgrade = (is_admin() && isset($_REQUEST['ezp_ibc_upgrade'])) || $re_init;

            if (($installed_ver != EZP_IBC_Constants::PLUGIN_VERSION) || $force_upgrade)
            {
                EZP_IBC_U::debug('updating tables');
                EZP_IBC_JSON_Entity_Base::init_table();
                EZP_IBC_Contact_Entity::init_table();
                EZP_IBC_Event_Entity::init_table();
                EZP_IBC_Public_ID_Entity::init_table();

                EZP_IBC_Global_Entity::initialize_plugin_data();

                EZP_IBC_Task_Scheduler::init();

                if($installed_ver == '0.5.0')
                {
                    EZP_IBC_U::debug("scoring all");
                    EZP_IBC_Contact_Entity::score_all();
                }
                    
                EZP_IBC_Options_U::set_option(EZP_IBC_Option_Subkeys::Plugin_Version, EZP_IBC_Constants::PLUGIN_VERSION);
            }
        }

        // [ezp-form id="x"]
        function render_form_shortcode($atts)
        {
            $a = shortcode_atts(array(
                'id' => 0,
                    ), $atts);

            $form_id = $a['id'];

            if ($form_id != 0)
            {
                $form = EZP_IBC_Form_Entity::get_by_id($form_id);

                if ($form != null)
                {
                    return $form->render();
                }
                else
                {
                    return EZP_IBC_U::__("Error rendering form $form_id");
                }
            }
            else
            {
                return EZP_IBC_U::__('Bad Form Shortcode');
            }
        }

        function add_class_action($tag, $method_name, $priority = 10, $accepted_args = 1)
        {
            return add_action($tag, array($this, $method_name), $priority, $accepted_args);
        }

        function add_class_filter($tag, $method_name, $priority = 10, $accepted_args = 1)
        {

            return add_filter($tag, array($this, $method_name), $priority, $accepted_args);
        }

//        // <editor-fold defaultstate="collapsed" desc="Hook Handlers">

        public static function activate()
        {

            //RSR TODO: move the table creation logic out of activate and into plugins loaded or something else since activate doesn't get called on upgrade
            EZP_IBC_U::debug("activate");
        }

        public static function deactivate()
        {

            EZP_IBC_U::debug("deactivate");
        }

//
//        // </editor-fold>
//
        public function enqueue_scripts()
        {
            
        }

        public function enqueue_admin_styles_support_page()
        {
            
        }

        public function enqueue_admin_styles_people_page()
        {
            
        }

        public function enqueue_scripts_people_page()
        {
            
        }

//        public function enqueue_scripts_lists_page()
//        {
//            
//        }

        public function enqueue_scripts_settings_page()
        {
            
        }

        public function enqueue_scripts_support_page()
        {
            
        }

        public function enqueue_scripts_form_page()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
        }

        public function enqueue_scripts_list_page()
        {
            
        }

        public function enqueue_scripts_dashboard_page()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
        }

        public function enqueue_scripts_page_trigger_page()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
        }

        public function enqueue_scripts_click_trigger_page()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
        }

//
//        /**
//         *  enqueue_admin_styles
//         *  Loads the required css links only for this plugin  */
        public function enqueue_admin_styles()
        {
            
        }

        public function enqueue_admin_styles_form_page()
        {
//            $jQueryPluginRoot = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG . "/jquery-plugins";
//
//            wp_enqueue_style('spectrum.css', $jQueryPluginRoot . '/spectrum-picker/spectrum.css', array(), EZP_CS_Constants::PLUGIN_VERSION);
        }

        public function enqueue_admin_styles_list_page()
        {
            
        }

        public function enqueue_admin_styles_dashboard_page()
        {
            $this->enqueue_jqueryui_styles();
        }

        public function enqueue_admin_styles_page_trigger_page()
        {
            $this->enqueue_jqueryui_styles();
        }

        private function enqueue_jqueryui_styles()
        {
            $style_root = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG . "/styles";

            $jquery_style_root = $style_root . '/jqueryui/1.11.2/themes/smoothness';

            wp_register_style('jquery-ui-min-css', $jquery_style_root . '/jquery-ui.min.css', array(), "1.11.2");
            wp_enqueue_style('jquery-ui-min-css');
        }

        public function enqueue_admin_styles_click_trigger_page()
        {
//            wp_register_style('jquery-ui-min-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', array(), EZP_IBC_Constants::PLUGIN_VERSION);
//            wp_enqueue_style('jquery-ui-min-css');
            $this->enqueue_jqueryui_styles();
        }

        public function enqueue_admin_styles_settings_page()
        {
            
        }

        public function enqueue_admin_styles_scripts_page()
        {
            
        }

        public function plugins_loaded_handler()
        {
            $this->init_options();
            $this->init_localization();
        }

        function init_options()
        {
            /* Screen options for list controls */
            $this->add_class_filter('set-screen-option', 'set_screen_option', 10, 3);
        }

        public function init_localization()
        {

            load_plugin_textdomain(EZP_IBC_Constants::PLUGIN_SLUG, false, EZP_IBC_Constants::PLUGIN_SLUG . '/languages/');
        }

        public function add_menu_pages()
        {

            // RSR TODO: not sure about perms for ibc right now
            $perms = 'manage_options';

            // Admin Menu
            add_menu_page('Site Spy', 'Site Spy', $perms, EZP_IBC_Constants::PLUGIN_SLUG, array($this, 'display_dashboard_page'), EZP_IBC_U::$PLUGIN_URL . '/images/74-location-lighter.png', '99.11325');

            $dashboard_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Dashboard'), __('Dashboard'), $perms, EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG, array($this, 'display_dashboard_page'));
            $people_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Contacts'), __('Contacts'), $perms, EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, array($this, 'display_people_page'));

            $user_events_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Intelligence'), __('Intelligence'), $perms, EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, array($this, 'display_user_events_page'));

            //$lists_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Lists'), __('Lists'), $perms, EZP_IBC_Constants::$LISTS_SUBMENU_SLUG, array($this, 'display_lists_page'));
            $settings_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Settings'), __('Settings'), $perms, EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG, array($this, 'display_settings_page'));
            $support_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::PLUGIN_SLUG, __('Support'), __('Support'), $perms, EZP_IBC_Constants::$SUPPORT_SUBMENU_SLUG, array($this, 'display_support_page'));

            // Pages not directly available from Admin Menu
            $contact_page_hook_suffix = add_submenu_page(null, __('Contact'), __('Contact'), $perms, EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, array($this, 'display_contact_page'));
            $form_page_hook_suffix = add_submenu_page(null, __('Form'), __('Form'), $perms, EZP_IBC_Constants::$FORM_SUBMENU_SLUG, array($this, 'display_form_page'));
            $list_page_hook_suffix = add_submenu_page(null, __('List'), __('List'), $perms, EZP_IBC_Constants::$LIST_SUBMENU_SLUG, array($this, 'display_list_page'));

            $user_event_type_page_hook_suffix = add_submenu_page(null, __('User Event Type'), __('User Event Type'), $perms, EZP_IBC_Constants::$USER_EVENT_TYPE_SLUG, array($this, 'display_user_event_type_page'));
            $page_trigger_page_hook_suffix = add_submenu_page(null, __('Page Trigger'), __('Page Trigger'), $perms, EZP_IBC_Constants::$PAGE_TRIGGER_SLUG, array($this, 'display_page_trigger_page'));
            // $click_trigger_page_hook_suffix = add_submenu_page(null, __('Click Trigger'), __('Click Trigger'), $perms, EZP_IBC_Constants::$CLICK_TRIGGER_SLUG, array($this, 'display_click_trigger_page'));
            $click_trigger_page_hook_suffix = add_submenu_page(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, __('Click Trigger'), __('Click Trigger'), $perms, EZP_IBC_Constants::$CLICK_TRIGGER_SLUG, array($this, 'display_click_trigger_page'));

            /* Screen Options */
            $this->add_class_action("load-$people_page_hook_suffix", 'add_contacts_screen_options');
            $this->add_class_action("load-$list_page_hook_suffix", 'add_list_members_screen_options');

            //-------- Enqueue Scripts
            add_action('admin_print_scripts-' . $people_page_hook_suffix, array($this, 'enqueue_scripts_people_page'));
            add_action('admin_print_scripts-' . $user_events_page_hook_suffix, array($this, 'enqueue_scripts'));
            add_action('admin_print_scripts-' . $dashboard_page_hook_suffix, array($this, 'enqueue_scripts_dashboard_page'));
            //add_action('admin_print_scripts-' . $lists_page_hook_suffix, array($this, 'enqueue_scripts'));
            add_action('admin_print_scripts-' . $settings_page_hook_suffix, array($this, 'enqueue_scripts_settings_page'));
            add_action('admin_print_scripts-' . $support_page_hook_suffix, array($this, 'enqueue_scripts_support_page'));
            add_action('admin_print_scripts-' . $contact_page_hook_suffix, array($this, 'enqueue_scripts'));
            add_action('admin_print_scripts-' . $form_page_hook_suffix, array($this, 'enqueue_scripts_form_page'));
            add_action('admin_print_scripts-' . $list_page_hook_suffix, array($this, 'enqueue_scripts_list_page'));
            add_action('admin_print_scripts-' . $page_trigger_page_hook_suffix, array($this, 'enqueue_scripts_page_trigger_page'));
            add_action('admin_print_scripts-' . $click_trigger_page_hook_suffix, array($this, 'enqueue_scripts_click_trigger_page'));


            //----------Apply Styles
            add_action('admin_print_styles-' . $people_page_hook_suffix, array($this, 'enqueue_admin_styles_people_page'));
            add_action('admin_print_styles-' . $user_events_page_hook_suffix, array($this, 'enqueue_admin_styles'));
            add_action('admin_print_styles-' . $dashboard_page_hook_suffix, array($this, 'enqueue_admin_styles_dashboard_page'));
            //add_action('admin_print_styles-' . $lists_page_hook_suffix, array($this, 'enqueue_admin_styles'));
            add_action('admin_print_styles-' . $settings_page_hook_suffix, array($this, 'enqueue_admin_styles_settings_page'));
            add_action('admin_print_styles-' . $support_page_hook_suffix, array($this, 'enqueue_admin_styles_support_page'));
            add_action('admin_print_styles-' . $contact_page_hook_suffix, array($this, 'enqueue_admin_styles'));
            add_action('admin_print_styles-' . $form_page_hook_suffix, array($this, 'enqueue_admin_styles_form_page'));
            add_action('admin_print_styles-' . $list_page_hook_suffix, array($this, 'enqueue_admin_styles_list_page'));
            add_action('admin_print_styles-' . $page_trigger_page_hook_suffix, array($this, 'enqueue_admin_styles_page_trigger_page'));
            add_action('admin_print_styles-' . $click_trigger_page_hook_suffix, array($this, 'enqueue_admin_styles_click_trigger_page'));
        }

        public function set_screen_option($status, $option, $value)
        {

            return $value;
        }

        public function add_contacts_screen_options()
        {
            $option = 'per_page';

            $args = array(
                'label' => EZP_IBC_U::__('Contacts'),
                'default' => 10,
                'option' => 'contacts_per_page'
            );

            add_screen_option($option, $args);
        }

        public function add_list_members_screen_options()
        {
            $option = 'per_page';

            $args = array(
                'label' => EZP_IBC_U::__('Contacts'),
                'default' => 10,
                'option' => 'list_members_per_page'
            );

            add_screen_option($option, $args);
        }

        function display_options_page($page)
        {
            $relative_page_path = '/../pages/' . $page;

            $__dir__ = dirname(__FILE__);

            include($__dir__ . $relative_page_path);
        }

        function display_people_page()
        {

            $this->display_options_page('/people/page-people.php');
        }

        function display_user_events_page()
        {
            $this->display_options_page('/intelligence/page-intelligence.php');
        }

        function display_user_event_type_page()
        {
            $this->display_options_page('/intelligence/page-user-event-type.php');
        }

        function display_page_trigger_page()
        {
            $this->display_options_page('/intelligence/page-page-trigger.php');
        }

        function display_click_trigger_page()
        {
            $this->display_options_page('/intelligence/page-click-trigger.php');
        }

        function display_list_page()
        {
            $this->display_options_page('/lists/page-list.php');
        }

        function display_dashboard_page()
        {
            $this->display_options_page('/dashboard/page-dashboard.php');
        }

        function display_settings_page()
        {
            $this->display_options_page('/settings/page-settings.php');
        }

        function display_support_page()
        {
            $this->display_options_page('/support/page-support.php');
        }

//        function display_lists_page()
//        {
//            $this->display_options_page('/lists/page-lists.php');
//        }

        function display_contact_page()
        {
            $this->display_options_page('/people/page-contact.php');
        }
    }
}