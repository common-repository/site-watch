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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-global-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-ecommerce.php');

$global = EZP_IBC_Global_Entity::get_instance();

$drop_tables_on_uninstall = EZP_IBC_Options_U::get_option(EZP_IBC_Option_Subkeys::Drop_Tables_On_Uninstall);
?>

<style lang="text/css">
    .compound-setting { line-height:25px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
    .description-div { margin-top:5px }
</style>

<div class="wrap">
    <div id="easypie-ibc-options" class="inside">
        <?php
        $action_updated = null;

        $error_string = '';

        if (isset($_POST['action']) && $_POST['action'] == 'save') {

            if (isset($_POST['track_logged_in_users'])) {
                $_POST['track_logged_in_users'] = 1;
            } else {
                $_POST['track_logged_in_users'] = 0;
            }
            
            if (isset($_POST['_drop_tables_on_uninstall'])) {
                $drop_tables_on_uninstall = 1;
            } else {
                $drop_tables_on_uninstall = 0;
            }
            
            if (isset($_POST['_start_here_enabled'])) {
                $_POST['start_here_enabled'] = 1;
            } else {
                $_POST['start_here_enabled'] = 0;
            }
            
            EZP_IBC_Options_U::set_option(EZP_IBC_Option_Subkeys::Drop_Tables_On_Uninstall, $drop_tables_on_uninstall);

            $error_string = $global->set_post_variables($_POST);

            if ($error_string == '') {
                $global->save();
                $action_updated = true;
            }
        }
        ?>

        <input type="hidden" name="action" value="save"/>            
        <?php
        if ($error_string != '') :
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php endif; ?>

        <?php if ($action_updated) : ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Settings Saved.'); ?></span></p></div>
        <?php endif; ?>       
         
        <div class="postbox" >
            <div class="inside" > 
                <h3><?php _e("Help") ?></h3>
                <table class="form-table"> 
                    <tr>
                        <th scope="row">
                            <?php echo _e("\"Start Here\" Tab") ?>
                        </th>
                        <td>
                            <div class="compound-setting">
                                <?php
                                $start_here_checked = ($global->start_here_enabled == 0) ? '' : 'checked';
                                ?>
                                <input name="_start_here_enabled" type="checkbox" <?php echo $start_here_checked; ?>/><?php echo __('Enabled'); ?>
                            </div>
                        </td>
                    </tr>                        
                </table>
            </div>
        </div>
            
        <div class="postbox" >
            <div class="inside" > 
                <h3><?php _e("ECommerce") ?></h3>
                <table class="form-table"> 
                    <tr>
                        <th scope="row">
                            <?php echo _e("System") ?>
                        </th>
                        <td>
                            <div class="compound-setting">    
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->ecommerce_mode == EZP_IBC_ECommerce_Modes::NONE); ?> name="ecommerce_mode" value="<?php echo EZP_IBC_ECommerce_Modes::NONE; ?>"><?php EZP_IBC_U::_e('None'); ?></input>
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->ecommerce_mode == EZP_IBC_ECommerce_Modes::WOO_COMMERCE); ?> name="ecommerce_mode" value="<?php echo EZP_IBC_ECommerce_Modes::WOO_COMMERCE; ?>"><?php EZP_IBC_U::_e('WooCommerce'); ?></input>
                            </div>
                            <div class="description-div"><span class="description"><?php echo __('Capture purchase events'); ?></span></div>
                        </td>
                    </tr>                        
                </table>
            </div>
        </div>
            
        <div class="postbox" >
            <div class="inside" >
                <h3><?php _e("Uninstall") ?></h3>
                <table class="form-table">                         
                    <tr>
                        <th scope="row">
                            <?php echo _e("Plugin Tables") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                     
                                <?php
                                $drop_tables_checked = ($drop_tables_on_uninstall == 0) ? '' : 'checked';
                                ?>
                                <input name="_drop_tables_on_uninstall" type="checkbox" <?php echo $drop_tables_checked; ?>/><?php echo EZP_IBC_U::__('Drop on Uninstall'); ?>
                            </div>
                        </td>
                    </tr>                                                
                </table>
            </div>
        </div>


        <?php
        submit_button();
        ?>
    </div>
</div>