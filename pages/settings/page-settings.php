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

$active_tab = EZP_IBC_U::get_request_val('tab', 'basic');

$nonce_action = "$active_tab-tab";

if(isset($_REQUEST['action']))
{
    check_admin_referer($nonce_action);
}

$storage_help = '';

if($active_tab == 'storage')
{
    $text = __('Default settings are estimates based on lightly used site. Cater to your site needs.');
                
    $storage_help = "<i title='$text' class='fa fa-question-circle'></i>";
}
?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/settings/page-settings-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
    
    #easy-pie-ibc-settings i { margin-left: 5px; color: #aaa; }
    #easy-pie-tab-holder { margin-top: 25px; }
</style>

<div class="wrap">

    <h2><?php echo __('Settings') . ' - Site Spy'; ?></h2>

    <div id="easy-pie-ibc-settings" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . '&tab=basic' ?>" class="nav-tab <?php echo $active_tab == 'basic' ? 'nav-tab-active' : ''; ?>"><?php _e('Basic'); ?></a>  
            <a href="?page=<?php echo EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . '&tab=advanced' ?>" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>"><?php _e('Advanced'); ?></a>  
            <a href="?page=<?php echo EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . '&tab=storage' ?>" class="nav-tab <?php echo $active_tab == 'storage' ? 'nav-tab-active' : ''; ?>"><?php echo __('Storage') . $storage_help; ?></a>  
        </h2>
        <form id="easy-pie-ibc-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$SETTINGS_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" >             
            <?php wp_nonce_field($nonce_action) ?>
            <div id='easy-pie-tab-holder'>
                <?php
                
                if ($active_tab == 'basic')
                {
                    include 'page-settings-basic-tab.php';
                }
                else if ($active_tab == 'advanced')
                {
                    include 'page-settings-advanced-tab.php';
                }
                else
                {
                    include 'page-settings-storage-tab.php';
                }
                ?>
            </div>           

            <?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

