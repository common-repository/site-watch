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

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/intelligence/page-click-trigger.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<style>
    .easy-pie-form-template { border: solid lightgray 1px;} 
</style>


<?php
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-click-trigger-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-user-event-type-entity.php');

$nonce_action = "page-trigger";

$action_updated = false;
$action_created = false;

$user_event_type_created = false;

$entity_id = EZP_IBC_U::get_request_val('click_trigger_id', -1);

$creating_entity = ($entity_id == -1);

if ($creating_entity)
{
    $click_trigger = new EZP_IBC_Click_Trigger_Entity();
}
else
{
    $click_trigger = EZP_IBC_Click_Trigger_Entity::get_by_id($entity_id);
}

$error_string = "";

if (isset($_POST['action']))
{
    check_admin_referer($nonce_action);

    $action = $_POST['action'];

    if ($action == 'create_user_event_type')
    {
        //user_event_type_name        
        $user_event_type = new EZP_IBC_User_Event_Type_Entity();

        $error_string = $user_event_type->set_post_variables($_POST);

        if ($error_string == '')
        {
            $saved = $user_event_type->save();
            
            $click_trigger->user_event_type_id = $user_event_type->id;

            // May be false or a string with errors with 
            $user_event_type_created = true;
        }
        else
        {
            $error_string = _('Event Creation - ') . $error_string;
        }
    }
    else if ($action == 'save')
    {
        $error_string = $click_trigger->set_post_variables($_POST);

        if ($error_string == "")
        {
            if ($click_trigger->user_event_type_id == -1)
            {
                $error_string = _('No event selected. Select one or create a new one if needed.');
            }

            if ($error_string == '')
            {
                $saved = $click_trigger->save();

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

$click_triggers_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
$click_triggers_url = EZP_IBC_U::append_query_value($click_triggers_url, 'tab', 'click-triggers');

?>


<style lang="text/css">
    .compound-setting { line-height:20px;}

    .description-div { margin-top:5px;}
    .event-dialog-label { width: 95px; display: inline-block; }
</style>



<div class="wrap">
    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2><?php echo ($creating_entity == true ? __('Create Click Trigger') : __('Edit Click Trigger')) . ' - Site Spy'; ?></h2>
    <div><a href="<?php _e($click_triggers_url);?>"><?php _e('View All');?></a></div>
    <form id="easy-pie-ibc-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$CLICK_TRIGGER_SLUG); ?>" >             
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
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Click Trigger Updated.'); ?></div>
            <?php
        }
        else if ($action_created)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Click Trigger Created.'); ?></div>
            <?php
        }
        ?>

        <?php
        if ($user_event_type_created)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo __('Event') . ' ' . $_POST['name'] . ' ' . _('Created.'); ?></div>
        <?php }; ?>


        <input type="hidden" name="click_trigger_id" value="<?php echo $click_trigger->id; ?>"/>

        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >

                <table class="form-table">                                        
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Event") ?></span>
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
                                        if ($user_event_type->id == $click_trigger->user_event_type_id)
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
                                <div style="margin-left:28px"><a style="font-size: .8em" href="#" onclick="easyPie.IBC.UserEvents.PageClickTrigger.showCreateUserEventTypeRadio();
                                        return false;"><?php _e('Create New'); ?></a></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Selector") ?></span>
                        </th>
                        <td>  
                            <input style="width:350px;" name="selector" type="text" value="<?php echo $click_trigger->selector; ?>"/>
                            <div class="description-div"><span class="description"><?php echo sprintf(__('jQuery selector specfying one or more page elements. <a target="_blank" href="%1$s">What the heck does that mean?</a>'), 'http://easypiewp.com/selecting-page-elements-with-jquery/'); ?></span></div>
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
