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

$global = EZP_IBC_Global_Entity::get_instance();
/* @var $global EZP_IBC_Global_Entity */

if(isset($_POST['enable_start']))
{
    if($_POST['enable_start'] == 0)
    {                
        $global->start_here_enabled = false;
        $global->save();
        
        $_REQUEST['tab'] = 'site-status';
    }
}

if($global->start_here_enabled)
{
    $default_tab = 'start-here';        
    $start_here_display = '';
}
else
{
    $default_tab = 'people';
    $start_here_display = 'display:none';    
}

$active_tab = EZP_IBC_U::get_request_val('tab', $default_tab);

?>
<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/dashboard/page-dashboard-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<div class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2 id="easy-pie-ibc-display-name">
        <?php
        echo __('Dashboard') . ' - Site Spy';
        ?>
    </h2>

    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a style="<?php _e($start_here_display); ?>" href="?page=<?php echo EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG . '&tab=start-here' ?>" class="nav-tab <?php echo $active_tab == 'start-here' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Start Here'); ?></a>              
            <a href="?page=<?php echo EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG . '&tab=people' ?>" class="nav-tab <?php echo $active_tab == 'people' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('People'); ?><span style="margin-left:7px; display:none" id='easy-pie-realtime-status'></span></a>              
            <a href="?page=<?php echo EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG . '&tab=site-status' ?>" class="nav-tab <?php echo $active_tab == 'site-status' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Site'); ?></a>                          
            <a href="?page=<?php echo EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG . '&tab=realtime-monitor' ?>" class="nav-tab <?php echo $active_tab == 'realtime-monitor' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Real-time'); ?><span style="margin-left:7px; display:none" id='easy-pie-realtime-status'></span></a>              
            
        </h2>

        <div id='tab-holder'>
            <?php
            if ($active_tab == 'start-here')
            {                
                include 'page-dashboard-start-here-tab.php';
            }
            else if ($active_tab == 'site-status')
            {                
                include 'page-dashboard-site-status-tab.php';
            }
            else if ($active_tab == 'people')
            {
                include 'page-dashboard-people-tab.php';
            }
            else
            {
                include 'page-dashboard-realtime-monitor-tab.php';
            }
            ?>
        </div>           

        <?php EZP_IBC_U::echo_footer_links(); ?>        

    </div>
</div>

