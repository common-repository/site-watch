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

require_once(dirname(__FILE__) . '/../class-ezp-ibc-constants.php');

if (!class_exists('EZP_IBC_U'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_U
    {
        // Pseudo-constants
        public static $MINI_THEMES_TEMPLATE_DIRECTORY;
        public static $PLUGIN_URL;
        public static $PLUGIN_DIRECTORY;
        private static $type_format_array;

        public static function init()
        {

            $__dir__ = dirname(__FILE__);

            self::$MINI_THEMES_TEMPLATE_DIRECTORY = $__dir__ . "/../templates/";

            self::$PLUGIN_URL = plugins_url() . "/" . EZP_IBC_Constants::PLUGIN_SLUG;

            self::$PLUGIN_DIRECTORY = (WP_CONTENT_DIR . "/plugins/" . EZP_IBC_Constants::PLUGIN_SLUG);

            self::$type_format_array = array('boolean' => '%s', 'integer' => '%d', 'double' => '%g', 'string' => '%s');
        }

        public static function _e($text)
        {

            _e($text, EZP_IBC_Constants::PLUGIN_SLUG);
        }

        public static function __($text)
        {

            return __($text, EZP_IBC_Constants::PLUGIN_SLUG);
        }

        public static function _he($text)
        {

            echo htmlspecialchars($text);
        }

        public static function get_full_table_name($base_table_name)
        {
            global $wpdb;

            return $wpdb->prefix . $base_table_name;
        }

        public static function url_from_slug($slug)
        {

            return "?page=" . $slug;
        }

        public static function get_simplified_local_time_from_formatted_gmt($timestamp)
        {
            $local_ticks = EZP_IBC_U::get_local_ticks_from_gmt_formatted_time($timestamp);
            //F j, hh:MM meridian
            //return EZP_IBC_U::get_wp_formatted_from_gmt_formatted_time($item['timestamp']);

            $date_portion = date('M j,', $local_ticks);
            $time_portion = date('g:i:s a', $local_ticks);
                        
            return "$date_portion $time_portion";
        }
        
        public static function get_database_sizes()
        {
            global $wpdb;

            $database_size = (float) 0;
            $table_size = (float) 0;

            $query = "SELECT TABLE_NAME, round(((data_length + index_length) / 1024 / 1024),2) 'Size_MB' FROM information_schema.TABLES WHERE table_schema = '$wpdb->dbname'";

            $results = $wpdb->get_results($query);

            
            foreach ($results as $result)
            {
                $size = (float) $result->Size_MB;

                if (strpos($result->TABLE_NAME, 'easy_pie_ibc') != false)
                {
                    $table_size += $size;
                }

                $database_size += $size;
            }

            $ret_val = array();
            array_push($ret_val, $database_size);
            array_push($ret_val, $table_size);

            return $ret_val;
        }        
        
        public static function ticks_to_standard_formatted($ticks)
        {
            return date('Y-m-d H:i:s', $ticks);
        }

        public static function javascript_redirect($url)
        {
            echo "<script type='javascript'>window.location = '$url';</script>";
        }

        public static function starts_with($haystack, $needle)
        {
            $length = strlen($needle);

            return (substr($haystack, 0, $length) === $needle);
        }

        public static function get_local_ticks_from_gmt_formatted_time($timestamp)
        {
            $ticks = strtotime($timestamp);

            $ticks += ((int) get_option('gmt_offset') * 3600);

            return $ticks;
        }
        
        public static function get_formatted_local_time_from_gmt($timestamp, $format)
        {
            $ticks = self::get_local_ticks_from_gmt_formatted_time($timestamp);
            
            return date($format, $ticks);
        }

        public static function get_wp_formatted_from_gmt_formatted_time($timestamp, $include_date = true, $include_time = true)
        {
            $ticks = self::get_local_ticks_from_gmt_formatted_time($timestamp);

            $date_format = get_option('date_format');
            $time_format = get_option('time_format');

            //return date("Y-m-d H:i:s", $ticks);
            if ($include_date)
            {
                $date_portion = date($date_format, $ticks);
            }
            else
            {
                $date_portion = '';
            }

            if ($include_time)
            {
                $time_portion = date($time_format, $ticks);
            }
            else
            {
                $time_portion = '';
            }

            if ($include_date && $include_time)
            {
                $seperator = ' ';
            }
            else
            {
                $seperator = '';
            }

            return "$date_portion$seperator$time_portion";
        }

        public static function ends_with($haystack, $needle)
        {
            $length = strlen($needle);
            if ($length == 0)
            {
                return true;
            }

            return (substr($haystack, -$length) === $needle);
        }

        public static function get_page_name_from_url($url)
        {
            $post_id = url_to_postid($url);

            if ($post_id != 0)
            {
                $page_name = get_the_title($post_id);
            }
            else
            {
                $page_name = $url;
            }

            return $page_name;
        }

        public static function append_query_value($url, $key, $value)
        {
            $separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';

            $modified_url = $url . "$separator$key=$value";

            return $modified_url;
        }

        public static function get_db_type_format($variable)
        {

            $type_string = gettype($variable);

            if ($type_string == "NULL")
            {

                self::debug("get_db_type_format: Error. Variable is not initialized.");
                return "";
            }

            return self::$type_format_array[$type_string];
        }

        public static function echo_checked($val)
        {
            echo $val ? 'checked' : '';
        }

        public static function echo_selected($val)
        {
            echo $val ? 'selected' : '';
        }

        public static function get_pid_from_cookie($cookie)
        {
            $pid = null;

            if (isset($cookie[EZP_IBC_Constants::PID_COOKIE_NAME]))
            {
                $pid = $cookie[EZP_IBC_Constants::PID_COOKIE_NAME];

                EZP_IBC_U::debug("Found $pid in cookie");
            }
            else
            {
            //    EZP_IBC_U::debug_object('Cookie isnt set', $cookie);
            }

            return $pid;
        }

        public static function get_public_properties($object)
        {

            $publics = get_object_vars($object);
            unset($publics['id']);

            // Disregard anything that starts with '_'
            foreach ($publics as $key => $value)
            {
                if (EZP_IBC_U::starts_with($key, '_'))
                {
                    unset($publics[$key]);
                }
            }

            // rsr only in json types unset($publics['type']);

            return $publics;
        }

        public static function get_public_class_properties($class_name)
        {

            $publics = get_class_vars($class_name);
            unset($publics['id']);

            return $publics;
        }

        public static function get_guid()
        {

            if (function_exists('com_create_guid') === true)
            {
                return trim(com_create_guid(), '{}');
            }

            return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }

        public static function display_admin_notice($coming_soon_on)
        {
            if ($coming_soon_on)
            {

                echo "<div class='error'><a href='" . admin_url() . "admin.php?page=" . EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . "'>" . self::__("Coming Soon is On") . "</a></div>";
            }
            else
            {

                echo "<div style='text-decoration:underline' class='updated'><a href='" . admin_url() . "admin.php?page=" . EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . "'>" . self::__("Coming Soon is Off") . "</a></div>";
            }
        }

        public static function get_request_val($key, $default)
        {
            if (isset($_REQUEST[$key]))
            {
                return $_REQUEST[$key];
            }
            else
            {
                return $default;
            }
        }

        public static function debug($message, $to_screen = false)
        {
            $auditing_enabled = EZP_IBC_Options_U::get_option(EZP_IBC_Option_Subkeys::Execution_Auditing_On);

            $send_to_log = $auditing_enabled;
            $send_to_screen = $to_screen && $auditing_enabled;

            if (is_array($message) || is_object($message))
            {
                if ($send_to_log)
                {
                    error_log(EZP_IBC_Constants::PLUGIN_SLUG . ":" . print_r($message, true));
                }

                if ($send_to_screen)
                {
                    echo print_r($message, true);
                    echo '<br/>';
                }
            }
            else
            {
                if ($send_to_log)
                {
                    error_log(EZP_IBC_Constants::PLUGIN_SLUG . ":" . $message);
                }

                if ($send_to_screen)
                {
                    echo $message . '<br/>';
                }
            }
        }

        public static function debug_object($message, $object)
        {
            self::debug($message . '<br\>');
            self::debug($object);
        }

        public static function ddebug($message)
        {
            self::debug($message, true);
        }

        public static function is_current_url_unfiltered($config)
        {

            $requested = strtolower($_SERVER['REQUEST_URI']);

            $config->allowed_urls = strtolower($config->unfiltered_urls);
            $urls = preg_split('/\r\n|[\r\n]/', $config->unfiltered_urls);

            $is_unfiltered = false;
            foreach ($urls as $url)
            {

                $trimmed_url = trim($url);
                if ((strpos($requested, $trimmed_url) === 0))
                {

                    $is_unfiltered = true;
                    break;
                }
            }

            return $is_unfiltered;
        }

        public static function echo_footer_links()
        {
            //
            //$contacts_url = EZP_IBC_U::append_query_value($contacts_url, 'event_range', $event_range);  

            $dashboard_url = menu_page_url(EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG, false);
            
            $realtime_url = menu_page_url(EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG, false);
            $realtime_url = EZP_IBC_U::append_query_value($realtime_url, 'tab', 'realtime-monitor');

            $contacts_url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);

            $intelligence_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);

          //  $lists_url = menu_page_url(EZP_IBC_Constants::$LISTS_SUBMENU_SLUG, false);
            $settings_url = menu_page_url(EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG, false);
            
            $support_url = menu_page_url(EZP_IBC_Constants::$SUPPORT_SUBMENU_SLUG, false);

            echo "<div style='clear:both'>";

            //$site_status_text = __('Status');
            $dashboard_text = __('Dashboard');
            $realtime_text = __('Real-time');
            
            $contacts_text = __('Contacts');
            $intelligence_text = __('Intelligence');
            $settings_text = __('Settings');
            $support_text = __('Help');

            echo "<a href='$dashboard_url'>$dashboard_text</a> | ";
            echo "<a href='$realtime_url'>$realtime_text</a> | ";
            echo "<a href='$contacts_url'>$contacts_text</a> | ";
            echo "<a href='$intelligence_url'>$intelligence_text</a> | ";
            echo "<a href='$settings_url'>$settings_text</a> | ";
            echo "<a href='$support_url'>$support_text</a>";
            echo "</div>";
        }

        public static function get_php_file_output($filepath)
        {
            ob_start();
            include($filepath);
            $render = ob_get_clean();

            return $render;
        }
    }
    EZP_IBC_U::init();
}
?>