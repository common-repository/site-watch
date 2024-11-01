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
?>
<?php
$active_tab = EZP_IBC_U::get_request_val('tab', 'forms');

$add_new_url = "";

switch($active_tab)
{
    case 'forms':
        $add_new_url = "?page=" . EZP_IBC_Constants::$FORM_SUBMENU_SLUG;
        break;
}

?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/assets/page-assets-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2><?php EZP_IBC_U::_e('Forms & Buttons'); ?><a href="<?php echo $add_new_url?>" class="add-new-h2">Add New Form</a></h2>
    <?php EZP_IBC_U::echo_header_links(); ?>
    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$ASSETS_SUBMENU_SLUG . '&tab=forms' ?>" class="nav-tab <?php echo $active_tab == 'forms' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Forms'); ?></a>              
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$ASSETS_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" >             
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'forms')
                {
                    include 'page-assets-forms-tab.php';
                }
                ?>
            </div>    
            <?php EZP_IBC_U::echo_footer_links(); ?>
        </form>
    </div>
</div>