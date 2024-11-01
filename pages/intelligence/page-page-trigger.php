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

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/intelligence/page-page-trigger.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<style>
    .easy-pie-form-template { border: solid lightgray 1px;} 
</style>


<?php
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-page-trigger-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-user-event-type-entity.php');

$nonce_action = "page-trigger";

$action_updated = false;
$action_created = false;

$user_event_type_created = false;

$entity_id = EZP_IBC_U::get_request_val('page_trigger_id', -1);

$creating_entity = ($entity_id == -1);

if ($creating_entity)
{
    $page_trigger = new EZP_IBC_Page_Trigger_Entity();
}
else
{
    $page_trigger = EZP_IBC_Page_Trigger_Entity::get_by_id($entity_id);
}


$error_string = "";


if (isset($_POST['action']))
{
    check_admin_referer($nonce_action);

    $action = $_POST['action'];

    if ($action == 'create_user_event_type')
    {
        //user_event_type_name        
        $new_user_event_type = new EZP_IBC_User_Event_Type_Entity();

        $error_string = $new_user_event_type->set_post_variables($_POST);

        if ($error_string == '')
        {
            $saved = $new_user_event_type->save();

            // May be false or a string with errors with 
            $user_event_type_created = true;

            $page_trigger->user_event_type_id = $new_user_event_type->id;
        }
        else
        {
            echo "didnt save user event";
            $error_string = _('Event Creation - ') . $error_string;
        }
    }
    else if ($action == 'save')
    {
        // Artificially set the bools since they aren't part of the postback
        //    $display->background_tiling_enabled = "false";
        //EZP_IBC_U::ddebug($_POST);
        //EZP_IBC_U::ddebug(gettype($entity_id));
        $trigger_type = (int) $_POST['trigger_type'];

        switch ($trigger_type)
        {
            case EZP_IBC_Page_Trigger_Types::Page_ID:
                //  $_POST['post_id'] = $_POST['_page_id'];
                $page_trigger->post_ids = $_POST['_page_ids'];

                $page_trigger->urls = array();

                break;

            case EZP_IBC_Page_Trigger_Types::Post_ID:
                // post_id already set
                $post_id_string = trim($_POST['_post_ids']);
                if ($post_id_string == '')
                {
                    //$_POST['post_id'] = -1;
                    $page_trigger->post_ids = array();
                }
                else
                {
                    $page_trigger->post_ids = explode(',', $post_id_string);
                }

                $page_trigger->urls = array();

                break;

            case EZP_IBC_Page_Trigger_Types::URL:
                //$_POST['post_ids'] = array();                
                $page_trigger->urls = array($_POST['_url']);

                break;
        }

        $error_string = $page_trigger->set_post_variables($_POST);

        if ($error_string == "")
        {
            //$event_id = (int)$_POST["_event_id"];

            if ($page_trigger->user_event_type_id == -1)
            {
                $error_string = _('No event selected.');
            }

            if ($error_string == '')
            {
                $saved = $page_trigger->save();

                // May be false or a string with errors with 
                if ($creating_entity)
                {
                    $action_created = true;
                    $creating_entity = false;
                }
                else
                {
                    $action_updated = true;
                }
            }
        }
        else
        {
            // its either a false or an error
        }

        EZP_IBC_U::debug("action saved, action updated=$action_updated");
    }
}

$page_triggers_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
$page_triggers_url = EZP_IBC_U::append_query_value($page_triggers_url, 'tab', 'page-triggers');
?>

<style lang="text/css">
    .compound-setting { line-height:20px;}

    .event-dialog-label { width: 95px; display: inline-block; }
</style>



<div class="wrap">
        <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2><?php echo $creating_entity == true ? __('Create Page Trigger') : __('Edit Page Trigger') . ' - Site Spy'; ?></h2>
    <div><a href="<?php _e($page_triggers_url); ?>"><?php _e('View All'); ?></a></div>
    <form id="easy-pie-ibc-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$PAGE_TRIGGER_SLUG); ?>" >             
        <?php wp_nonce_field($nonce_action); ?>
        <input type="hidden" name="action" value="save"/>
        <?php
        if ($error_string != "")
        {
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php }; ?>

        <?php
        if ($action_updated)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Trigger Updated.'); ?></div>
            <?php
        }
        else if ($action_created)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Trigger Created.'); ?></div>
            <?php
        }
        ?>

        <?php
        if ($user_event_type_created)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo __('User Event') . ' ' . $_POST['name'] . ' ' . _('Created.'); ?></div>
<?php }; ?>


        <input type="hidden" name="page_trigger_id" value="<?php echo $page_trigger->id; ?>"/>

        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >

                <table class="form-table">                                        
                    <tr>
                        <th scope="row">
                            <span><?php echo _e('User Event') ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">  

                                <select name="user_event_type_id" onchange="easyPie.IBC.UserEvents.PagePageTrigger.ChangeEventNameState(this.selectedIndex !== 0);"> 
                                    <option value="-1" onselect=""><?php echo EZP_IBC_U::__('Select Existing'); ?></option> 
                                    <?php
                                    $user_event_types = EZP_IBC_User_Event_Type_Entity::get_all();

                                    foreach ($user_event_types as $user_event_type)
                                    {
                                        /*
                                         * @var $user_event_type EZP_IBC_User_Event_Type_Entity
                                         */
                                        if ($user_event_type->id == $page_trigger->user_event_type_id)
                                        {
                                            $selected_string = 'selected';
                                        }
                                        else
                                        {
                                            $selected_string = '';
                                        }

                                        echo "<option $selected_string value='$user_event_type->id'>$user_event_type->name</option>";
                                    }
                                    ?>
                                </select>
                                <!--                                <button onclick="easyPie.IBC.UserEvents.PagePageTrigger.showCreateUserEventTypeRadio();
                                                                        return false;" style="margin-left:10px" class="button button-secondary"><?php _e('Create New'); ?></button>-->
                                <div style="margin-left:28px"><a style="font-size: .8em" href="#" onclick="easyPie.IBC.UserEvents.PagePageTrigger.showCreateUserEventTypeRadio();
                                        return false;"><?php _e('Create New'); ?></a></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <span><?php echo _e('Page') ?></span>
                        </th>

                        <td>  
                            <table style="margin-left:-53px">
                                <tr>
                                    <td>
                                        <input <?php EZP_IBC_U::echo_checked($page_trigger->trigger_type == EZP_IBC_Page_Trigger_Types::Page_ID) ?> id="easy-pie-ibc-select-page-radio" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectTrigger(0);" type="radio" name="trigger_type" value="<?php _e(EZP_IBC_Page_Trigger_Types::Page_ID) ?>"/>
                                    </td>
                                    <td>
                                        <select size='7' multiple id="easy-pie-ibc-select-page" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectRadio(0);" name="_page_ids[]"> 
                                            <?php
                                            {
                                                $pages = get_pages();

                                                foreach ($pages as $page)
                                                {
                                                    $selected_string = '';
                                                    if ($page_trigger->trigger_type == EZP_IBC_Page_Trigger_Types::Page_ID)
                                                    {
                                                        if (in_array($page->ID, $page_trigger->post_ids))
                                                        {
                                                            $selected_string = 'selected';
                                                        }
                                                    }

                                                    echo "<option $selected_string value='$page->ID'>$page->post_title</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input <?php EZP_IBC_U::echo_checked($page_trigger->trigger_type == EZP_IBC_Page_Trigger_Types::Post_ID) ?> id="easy-pie-ibc-get-post-id-radio" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectTrigger(1);" type="radio" name="trigger_type" value="<?php _e(EZP_IBC_Page_Trigger_Types::Post_ID) ?>"/>
                                    </td>
                                    <td>
                                        <?php
                                        if ($page_trigger->trigger_type != EZP_IBC_Page_Trigger_Types::Post_ID)
                                        {
                                            $post_id_value = '';
                                        }
                                        else
                                        {
                                            $post_id_value = implode(',', $page_trigger->post_ids);
                                        }
                                        ?>
                                        <input name="_post_ids" id="easy-pie-ibc-get-post-id" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectRadio(1);" easy-pie-ibc-select-page type="text" placeholder="<?php _e('Enter Post ID') ?>" value="<?php _e($post_id_value) ?>"/>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input <?php EZP_IBC_U::echo_checked($page_trigger->trigger_type == EZP_IBC_Page_Trigger_Types::URL) ?> id="easy-pie-ibc-get-url-radio" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectTrigger(2);" type="radio" name="trigger_type" value="<?php _e(EZP_IBC_Page_Trigger_Types::URL) ?>"/>
                                    </td>
                                    <td>
                                        <input name="_url" id="easy-pie-ibc-get-url" onclick="easyPie.IBC.UserEvents.PagePageTrigger.SelectRadio(2);" type="text" placeholder="<?php _e('Enter Relative URL') ?>" value="<?php echo count($page_trigger->urls) > 0 ? $page_trigger->urls[0] : ''; ?>"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>                    


                </table>
            </div>

        </div>        

        <p>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $creating_entity ? _e('Save') : _e('Update'); ?>" />
        </p>                

        <div>
<?php EZP_IBC_U::echo_footer_links(); ?>
        </div>
    </form>
</div>

<div id="easy-pie-ibc-new-user-event-type-form" title="<?php _e('Create User Event'); ?>"  style="display:none">
    <form  method="post">
        <input type="hidden" name="action" value="create_user_event_type" />
        <input type="hidden" name="page" value="<?php _e(EZP_IBC_Constants::$PAGE_TRIGGER_SLUG) ?>" />
<?php wp_nonce_field($nonce_action) ?>
        <div style="margin-top:10px">
            <span class="event-dialog-label"><?php _e('Name'); ?></span>
            <input style='width: 165px;' class="event-dialog-input" name="name" />
        </div>
        <div style='margin:15px 0;'>
            <span class="event-dialog-label"><?php _e('Description'); ?></span>
            <input style='width: 255px;' name="description" />
        </div>
        <div style='margin:15px 0;'>
            <span class="event-dialog-label"><?php _e('Worth'); ?></span>
            <input style='width: 44px;' name="worth" value="0" />
        </div>
        <div style='text-align:center; margin-top: 26px;'>
            <button class='button'><?php _e('Create'); ?></button>
            <button type='button' style='margin-left:10px' class='button' onclick="jQuery('#easy-pie-ibc-new-user-event-type-form').dialog('close');
                    return false;"><?php _e('Cancel'); ?></button>
        </div>
    </form>
</div>
