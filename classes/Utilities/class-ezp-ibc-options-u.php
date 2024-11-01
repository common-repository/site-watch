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

if (!class_exists('EZP_IBC_Option_Subkeys'))
{
    abstract class EZP_IBC_Option_Subkeys
    {
        const Plugin_Version = 'plugin-version';
        const Drop_Tables_On_Uninstall = 'drop-tables-on-uninstall';
        const Execution_Auditing_On = 'execution-auditing-on';
    }       
}

if (!class_exists('EZP_IBC_Options_U')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Options_U {
        
        const MAIN_OPTION_KEY = 'easy-pie-ibc-options';                
        
        public static function get_all_options()
        {
            $options = get_option(EZP_IBC_Constants::MAIN_OPTION_KEY);

            if ($options == false)
            {

                $options = array();
            }

            return $options;
        }
        
        public static function set_option($subkey, $value)
        {
            $options = self::get_all_options();
            
            self::set_cached_option($options, $subkey, $value);
            
            self::update_options($options);
        }

        public static function get_option($subkey, $default = '')
        {
            $options = self::get_all_options();
            
            return self::get_cached_option($options, $subkey, $default);
        }
        
        public static function get_cached_option(&$option_array, $subkey, $default = '')
        {
            if (array_key_exists($subkey, $option_array))
            {
                return $option_array[$subkey];
            }
            else
            {
                return $default;
            }
            
            return $optionArray[strtolower($subkey)];
        }

        public static function set_default_cached_option(&$option_array, $subkey, $value)
        {
            if (!array_key_exists($subkey, $option_array))
            {
                $option_array[$subkey] = $value;
            }
        }

        public static function set_cached_option(&$option_array, $subkey, $value)
        {
            $option_array[$subkey] = $value;
        }

        public static function update_options($options)
        {
            update_option(EZP_IBC_Constants::MAIN_OPTION_KEY, $options);
        }  

    }      
}
?>