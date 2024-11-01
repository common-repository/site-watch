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

$active_tab = EZP_IBC_U::get_request_val('tab', 'contacts');

$nonce_action = "$active_tab-tab";

if(isset($_REQUEST['form_action']))
{
    check_admin_referer($nonce_action);
}

//$contact_id = EZP_IBC_U::get_request_val('contact_id', -1);

//$contact = EZP_IBC_Contact_Entity::get_by_id($contact_id);

//echo "<p>" . print_r($contact) . '</p';

/* Logic that should be in the edit tab */
$action_updated = null;

$error_string = "";

//$readonly = $contact->wpid == - 1 ? '' : 'readonly';

//if (isset($_POST['form_action']) && $_POST['form_action'] == 'save')
//{
//
//    // Artificially set the bools since they aren't part of the postback
////    $display->background_tiling_enabled = "false";
//
//  
//    
//}
?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/people/page-people-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2 id="easy-pie-ibc-display-name">
        <?php                 
            echo __('Contacts') . ' - Site Spy';
            
         //   $page_trigger_help = '';
         //   $click_trigger_help = '';
         //   $user_events_help = '';
            
            if($active_tab == 'contacts')
            {
            //    $add_slug = EZP_IBC_Constants::$PAGE_TRIGGER_SLUG;
                $add_text = __('Add Contact');   
                //$text = __('User Event is triggered when user hits page(s).');
                
            //    $page_trigger_help = "<i title='$text' class='fa fa-question-circle'></i>";
            }
            else
            {
            //    $add_slug = EZP_IBC_Constants::$CLICK_TRIGGER_SLUG;
                $add_text = __('Add List');
                
              //  $text = __('User Event is triggered when user clicks element(s).');
                
            //    $click_trigger_help = "<i title='$text' class='fa fa-question-circle'></i>";


                $url = EZP_IBC_U::append_query_value('', 'page', EZP_IBC_Constants::$LIST_SUBMENU_SLUG);

                _e("<a href='$url' class='add-new-h2'>$add_text</a>");
            }
            
            
            
                                       
        ?>
    </h2>

    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG . '&tab=contacts' ?>" class="nav-tab <?php echo $active_tab == 'contacts' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Contacts'); ?></a>  
            <a href="?page=<?php echo EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG . '&tab=lists' ?>" class="nav-tab <?php echo $active_tab == 'lists' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Lists'); ?></a>  
        </h2>
        <form id="easy-pie-cs-main-form" method="get" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" >             
            <?php wp_nonce_field($nonce_action, '_wpnonce', false); ?>
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'contacts')
                {
                    include 'page-people-contacts-tab.php';
                }
                else
                {
                    include dirname(__FILE__) . '/../lists/page-lists-tab.php';
                }
                ?>
            </div>           

            <input type="hidden" id="ezp-cs-submit-type" name="ezp-cs-submit-type" value="save"/>

            <?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

