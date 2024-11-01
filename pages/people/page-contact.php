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

$active_tab = EZP_IBC_U::get_request_val('tab', 'edit');

$nonce_action = "$active_tab-tab";

if(isset($_REQUEST['form_action']))
{
    check_admin_referer($nonce_action);
}

$contact_id = EZP_IBC_U::get_request_val('contact_id', -1);

$contact = EZP_IBC_Contact_Entity::get_by_id($contact_id);

//echo "<p>" . print_r($contact) . '</p';

/* Logic that should be in the edit tab */
$action_updated = null;
$action_rescored = false;

$error_string = "";

$readonly = $contact->wpid == - 1 ? '' : 'readonly';

if (isset($_POST['form_action']))
{
    $form_action = $_POST['form_action'];
    
    if($form_action == 'save')
    {

        // Artificially set the bools since they aren't part of the postback
    //    $display->background_tiling_enabled = "false";

        $error_string = $contact->set_post_variables($_POST);

        EZP_IBC_U::debug("error string $error_string");
        if ($error_string == "")
        {
            $action_updated = $contact->save();
        }

        EZP_IBC_U::debug("action saved, action updated=$action_updated");
    }
    else if($form_action == 're_score')
    {
        $contact->rescore();
        
        $contact->save();
        
        $action_rescored = true;
    }
}
?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/people/page-contact-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2 id="easy-pie-ibc-display-name">
        <?php 
        $display_name = $contact->get_display_name();
        
        echo $display_name . ' - Site Spy';
        ?>
    </h2>

    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG . '&tab=edit' . '&contact_id=' . $contact_id ?>" class="nav-tab <?php echo $active_tab == 'edit' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Edit'); ?></a>  
            <a href="?page=<?php echo EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG . '&tab=events' . '&contact_id=' . $contact_id ?>" class="nav-tab <?php echo $active_tab == 'events' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Events'); ?></a>  
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" >             
            <?php wp_nonce_field($nonce_action); ?>
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'edit')
                {
                    include 'page-contact-edit-tab.php';
                }
                else
                {
                    include 'page-contact-events-tab.php';
                }
                ?>
            </div>           

            <input type="hidden" id="ezp-cs-submit-type" name="ezp-cs-submit-type" value="save"/>

            <?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

