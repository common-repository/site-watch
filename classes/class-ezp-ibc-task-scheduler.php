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

require_once(dirname(__FILE__) . '/class-ezp-ibc-storage-manager.php');

if (!class_exists('EZP_IBC_Task_Scheduler')) {

    /**     
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Task_Scheduler {
        
        const HOURLY_HOOK = 'easy-pie-ibc-hourly';
        
        public static function init()
        {
            self::install_hourly_cron_job();
        }
        
        public static function install_hourly_cron_job()
        {
            if (!wp_next_scheduled(self::HOURLY_HOOK)) 
            {                            
                $timestamp = time();

                // todo schedule hourly task
                $ret_val = wp_schedule_event($timestamp, 'hourly', self::HOURLY_HOOK);

                if($ret_val == false)
                {
                    EZP_IBC_U::debug("failed to set up hourly cron");
                }
                else if($ret_val == null)
                {
                    EZP_IBC_U::debug("sucecssfully set up hourly cron");   
                } else
                {
                    EZP_IBC_U::debug("unknown value $ret_val returned from wp_schedule_event");   
                }
            }           
        }
        
        public function register_actions()
        {            
            $this->add_class_action(self::HOURLY_HOOK, 'run_hourly_tasks');
        }
        
        public function run_hourly_tasks()
        {
            EZP_IBC_U::debug("run hourly tasks");
            
            $storage_manager = new EZP_IBC_Storage_Manager();
            
            $storage_manager->run_maintenance_cycle();
        }
          
        private function add_class_action($tag, $method_name, $priority=10, $accepted_args=1)
        {
            return add_action($tag, array($this, $method_name), $priority, $accepted_args);
        }
    }      
}
?>