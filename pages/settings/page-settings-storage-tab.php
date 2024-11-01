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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-event-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-global-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-ecommerce.php');

$global = EZP_IBC_Global_Entity::get_instance();

$action_updated = null;
$action_all_records_purged = null;

$error_string = '';

if (isset($_POST['action']))
{
    $post_action = $_POST['action'];

    if ($post_action == 'save')
    {
        $error_string = $global->set_post_variables($_POST);

        if ($error_string == '')
        {
            $global->save();
            $action_updated = true;
        }
    }
    else if ($post_action == 'purge_all_records')
    {
        EZP_IBC_Contact_Entity::delete_all();   // should ripple through and 
        EZP_IBC_Event_Entity::delete_all();
        EZP_IBC_Public_ID_Entity::delete_all();

        $action_all_records_purged = true;
    }
}

$event_count = EZP_IBC_Event_Entity::get_count_where();
$prospect_count = EZP_IBC_Contact_Entity::get_count_where('stage = ' . EZP_IBC_Contact_Stages::Prospect);

$sizes = EZP_IBC_U::get_database_sizes();
?>

<style lang="text/css">
    .compound-setting { line-height:25px;}
    .narrow-input { width:66px;}
    .medium-input { width:80px; }
    .long-input { width: 345px;}
    .description-div { margin-top:5px }
    .easy-pie-stats-table th { width: 138px;}
</style>

<div class="wrap">
    <div id="easypie-ibc-options" class="inside">

        <input type="hidden" name="action" value="save"/>            
        <?php
        if ($error_string != '') :
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php endif; ?>

        <?php if ($action_updated) : ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Settings Saved.'); ?></span></p></div>
        <?php endif; ?>

        <?php if ($action_all_records_purged) : ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('All Records Purged.'); ?></span></p></div>
        <?php endif; ?>

        <div class="postbox" >            
            <div class="inside" >
                <h3 style="margin-bottom:0px;"><?php _e("Stats") ?></h3>
                <table style="width:875px;">
                    <tr>
                        <td>
                            <table class="form-table easy-pie-stats-table"> 
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("WordPress DB Size") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo $sizes[0] . ' ' . __('MB'); ?></div>
                                        </div>
                                    </td>
                                </tr>                                                                                        
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("IBC Table Size") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo $sizes[1] . ' ' . __('MB'); ?></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td>
                            <table class="form-table easy-pie-stats-table"> 
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("# Prospects") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo number_format($prospect_count); ?></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("# Events") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo number_format($event_count); ?></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <button type="submit" style="margin-top:10px;" onclick="return easyPie.IBC.Settings.PageSettings.StorageTab.purgeAllRecords();"><?php _e('Delete All'); ?></button>
            </div>
        </div>

        <div class="postbox" >            
            <div class="inside" >
                <h3><?php _e("Purge Policy") ?></h3>               

                <table class="form-table"> 
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Inactive Prospect Age") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                                     
                                <select name=storage_pp_max_inactive_prospect_age_in_days>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_max_inactive_prospect_age_in_days == 3) ?> value="3"><?php _e('3 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_max_inactive_prospect_age_in_days == 7) ?> value="7"><?php _e('7 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_max_inactive_prospect_age_in_days == 30) ?> value="30"><?php _e('30 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_max_inactive_prospect_age_in_days == 90) ?> value="90"><?php _e('90 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_max_inactive_prospect_age_in_days == -1) ?> value="-1"><?php _e('Keep Forever'); ?></option>
                                </select>              
                                <div class="description-div"><span class="description"><?php echo __('How long to keep inactive prospects (anonymous visitors)'); ?></span></div>
                            </div>
                        </td>
                    </tr>                                                                                        
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Page History Retention") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                                                            
                                <select name=storage_pp_page_history_retention_in_days>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_page_history_retention_in_days == 3) ?> value="7"><?php _e('3 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_page_history_retention_in_days == 7) ?> value="7"><?php _e('7 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_page_history_retention_in_days == 30) ?> value="30"><?php _e('30 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_page_history_retention_in_days == 90) ?> value="90"><?php _e('90 Days'); ?></option>
                                    <option <?php EZP_IBC_U::echo_selected($global->storage_pp_page_history_retention_in_days == -1) ?> value="-1"><?php _e('Keep Forever'); ?></option>
                                </select>              
                                <div class="description-div"><span class="description"><?php echo __('How long to keep Page View events'); ?></span></div>
                            </div>
                        </td>
                    </tr>
<!--                    <tr>
                        <th scope="row">
                    <?php echo EZP_IBC_U::_e("Max Pages Per Visitor") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input style="float:left; margin-right:5px" class="narrow-input" name="storage_pp_max_pages_per_visitor" type="text" value="<?php echo $global->storage_pp_max_pages_per_visitor ?>" />
                                <div><?php _e('pages'); ?></div>
                                <div class="description-div"><span class="description"><?php echo __('Max number of URL hits to retain per visitor. Oldest is purged first. Set to 0 to disable.'); ?></span></div>
                            </div>
                        </td>
                    </tr>-->
                </table>                
            </div>
        </div>

        <div class="postbox" >            
            <div class="inside" >
                <h3><?php _e("Alerts") ?></h3>
                <table class="form-table"> 
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("WordPress DB Size") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input style="float:left; margin-right:5px" class="medium-input" name="storage_alerts_wordpress_db_size_in_MB" type="text" value="<?php echo $global->storage_alerts_wordpress_db_size_in_MB ?>" />
                                <div><?php _e('MB'); ?></div>
                                <div class="description-div"><span class="description"><?php echo __('Display alert if total size of WordPress database exceeds specified size. Set to 0 to disable.'); ?></span></div>
                            </div>
                        </td>
                    </tr>                                                                                        
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("IBC Table Size") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input style="float:left; margin-right:5px" class="medium-input" name="storage_alerts_ibc_table_size_in_MB" type="text" value="<?php echo $global->storage_alerts_ibc_table_size_in_MB ?>" />
                                <div><?php _e('MB'); ?></div>
                                <div class="description-div"><span class="description"><?php echo __('Display alert if size of Easy Pie IBC tables exceeds specified size. Set to 0 to disable.'); ?></span></div>
                            </div>
                        </td>
                    </tr>                 
                </table>
            </div>
        </div>                                     

        <div style='float:left; margin-right:15px;'>
            <?php
            submit_button();
            ?>
        </div>
    </div>
</div>