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

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/intelligence/page-user-event-type.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<style>
    .easy-pie-form-template { border: solid lightgray 1px;} 
</style>

<?php
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-user-event-type-entity.php');

$nonce_action = "event-type";

$action_updated = false;
$action_created = false;

$entity_id = EZP_IBC_U::get_request_val('user_event_type_id', -1);

$creating_entity = (bool) ($entity_id == -1);

if ($creating_entity)
{
    $user_event_type = new EZP_IBC_User_Event_Type_Entity();
} else
{
    $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id($entity_id);
}

$error_string = "";


if (isset($_POST['action']))
{
    check_admin_referer($nonce_action);

    $action = $_POST['action'];

    if ($action == 'save')
    {
        // Artificially set the bools since they aren't part of the postback
        //    $display->background_tiling_enabled = "false";
        //EZP_IBC_U::ddebug($_POST);
        //EZP_IBC_U::ddebug(gettype($entity_id));
        $error_string = $user_event_type->set_post_variables($_POST);

        if ($error_string == '')
        {
            $saved = $user_event_type->save();

            if ($creating_entity)
            {
                $action_created = true;
                $creating_entity = false;
            } else
            {
                $action_updated = true;
            }
        } else
        {
            // its either a false or an error
        }

        EZP_IBC_U::debug("action saved, action updated=$action_updated");
    }
}

$user_event_types_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
$user_event_types_url = EZP_IBC_U::append_query_value($user_event_types_url, 'tab', 'types');
?>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>



<div class="wrap">
<?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2><?php echo $creating_entity == true ? __('Create User Event') : __('Edit User Event') . ' - Site Spy'; ?></h2>
    <div><a href="<?php _e($user_event_types_url); ?>"><?php _e('View All'); ?></a></div>
    <form id="easy-pie-ibc-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$USER_EVENT_TYPE_SLUG); ?>" >             
<?php wp_nonce_field($nonce_action); ?>
        <input type="hidden" name="action" value="save"/>
        <?php
        if ($error_string != "") :
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php endif; ?>

        <?php
        if ($action_updated)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('User Event Updated.'); ?></div>
        <?php 
        
        } 
        else  if($action_created)
        {
            ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('User Event Created.'); ?></div>
            <?php
        }
        
        ?>

        <input type="hidden" name="user_event_type_id" value="<?php echo $user_event_type->id; ?>"/>

        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >

                <table class="form-table">                                        
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Name") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="long-input" name="name" type="text" value="<?php echo $user_event_type->name; ?>" />
                            </div>
                        </td>
                    </tr>                    

                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Description") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="long-input" name="description" type="text" value="<?php echo $user_event_type->description; ?>" />
                            </div>
                        </td>
                    </tr>                    
                    
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Worth") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="narrow-input" name="worth" type="text" value="<?php echo $user_event_type->worth; ?>" />
                                <div class="description-div"><span class="description"><?php echo sprintf(__('Added to user\'s score when event is triggered. Valid range is %1$d to %2$d.'), EZP_IBC_Score_Range::Min, EZP_IBC_Score_Range::Max); ?></span></div>
                            </div>
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

