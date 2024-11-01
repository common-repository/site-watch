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

$error_string = '';
$action_updated = '';
$active_tab = EZP_IBC_U::get_request_val('tab', 'edit');

$nonce_action = "$active_tab-tab";

if (isset($_REQUEST['form_action']))
{
    check_admin_referer($nonce_action);
}

$list_id = EZP_IBC_U::get_request_val('list_id', -1);


if ($list_id == -1)
{
    $list = new EZP_IBC_List_Entity();
}
else
{
    $list = EZP_IBC_List_Entity::get_by_id($list_id);
}

if (isset($_REQUEST['form_action']))
{
    $form_action = $_REQUEST['form_action'];

    if ($form_action == 'save')
    {
        /* @var list $EZP_IBC_List_Entity */
        $error_string = $list->set_post_variables($_REQUEST);

        if ($error_string == '')
        {
            $action_updated = $list->save();
        }
    }
    else if ($form_action == 'remove')
    {
        if (isset($_REQUEST['member_id']))
        {
            $member_id = $_REQUEST['member_id'];

            //EZP_IBC_Contact_Entity::delete_by_id($contact_id);
            $member = EZP_IBC_Contact_Entity::get_by_id($member_id);

            if ($member != null)
            {
                $member->remove_from_list($list_id);
                $action_updated = $member->save();
            }
            else
            {
                EZP_IBC_U::debug("attempting to delete remove member $member_id from list $list_id and it doesn't exist ");
            }
        }
    }
}

$creating_list = (bool) ($list->id == -1);

$members_display = $creating_list ? 'style="display:none"' : '';
?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/lists/page-list-$active_tab-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">

        <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2 id="easy-pie-ibc-display-name">
<?php echo ($creating_list ? __('New List') : __('List ') . $list->name) . ' - Site Spy'; ?>
    </h2>

    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$LIST_SUBMENU_SLUG . '&tab=edit' . '&list_id=' . $list_id ?>" class="nav-tab <?php echo $active_tab == 'edit' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Edit'); ?></a>  
            <a <?php echo $members_display; ?> href="?page=<?php echo EZP_IBC_Constants::$LIST_SUBMENU_SLUG . '&tab=members' . '&list_id=' . $list_id ?>" class="nav-tab <?php echo $active_tab == 'members' ? 'nav-tab-active' : ''; ?>"><?php EZP_IBC_U::_e('Members'); ?></a>  
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$LIST_SUBMENU_SLUG . '&tab=' . $active_tab . '&list_id=' . $list_id); ?>" >             
                <?php wp_nonce_field($nonce_action) ?>
            <div id='tab-holder'>
                <?php
                if ($active_tab == 'edit')
                {
                    include 'page-list-edit-tab.php';
                }
                else
                {
                    include 'page-list-members-tab.php';
                }
                ?>
            </div>           

            <input type="hidden" id="ezp-cs-submit-type" name="ezp-cs-submit-type" value="save"/>

<?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

