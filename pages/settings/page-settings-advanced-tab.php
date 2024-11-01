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
?>

<style lang="text/css">
    #easypie-ibc-options .compound-setting { line-height:25px; margin-top:3px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
    .description-div { margin-top:2px }
    
</style>

<div class="wrap">

    <div id="easypie-ibc-options" class="inside">
        <?php
        $action_updated = null;

        $error_string = '';

        if (isset($_POST['action']) && $_POST['action'] == 'save')
        {
            $error_string = $global->set_post_variables($_POST);

            if ($error_string == '')
            {
                EZP_IBC_U::debug("saving global");
                $global->save();
                $action_updated = true;
            }
            else
            {
                EZP_IBC_U::debug("there is an error saving global $error_string ?");
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
                <h3><?php _e("Form Submit") ?></h3>
                <table class="form-table"> 
                    <tr>
                        <th scope="row">
                            <?php echo _e("Forms") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="long-input" name="form_capture_list" type="text" value="<?php echo $global->form_capture_list; ?>" />
                            </div>
                            <div class="description-div"><span class="description"><?php echo sprintf(__('Comma separated list of <a target="_blank" href="%1$s">jQuery selectors</a> (e.g. #form-id, .form-class)'), 'http://easypiewp.com/selecting-page-elements-with-jquery/'); ?></span></div>
                        </td>
                    </tr>    
                    <tr>
                        <th scope="row">
                            <?php echo _e("Mode") ?>
                        </th>
                        <td>
                            <div class="compound-setting">    
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->form_capture_mode == EZP_IBC_Form_Capture_Modes::Capture_All_Except_List); ?> name="form_capture_mode" value="<?php echo EZP_IBC_Form_Capture_Modes::Capture_All_Except_List; ?>"><?php EZP_IBC_U::_e('Capture all forms except those listed'); ?></input><br/>
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->form_capture_mode == EZP_IBC_Form_Capture_Modes::Capture_Only_List); ?> name="form_capture_mode" value="<?php echo EZP_IBC_Form_Capture_Modes::Capture_Only_List; ?>"><?php EZP_IBC_U::_e('Capture only the listed forms'); ?></input>
                            </div
                        </td>
                    </tr>
                </table>
            </div>
        </div>
            
        <div class="postbox" >
            <div class="inside" > 
                <h3><?php _e('Communications') ?></h3>
                <table class="form-table">                     
                    <tr>
                        <th scope="row">
                            <?php echo _e('Ajax Protocol') ?>
                        </th>
                        <td>
                            <div class="compound-setting">    
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->ajax_protocol == EZP_IBC_Ajax_Protocols::Auto); ?> name="ajax_protocol" value="<?php echo EZP_IBC_Ajax_Protocols::Auto; ?>"><?php EZP_IBC_U::_e('Auto'); ?></input><br/>
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->ajax_protocol == EZP_IBC_Ajax_Protocols::Http); ?> name="ajax_protocol" value="<?php echo EZP_IBC_Ajax_Protocols::Http; ?>"><?php EZP_IBC_U::_e('HTTP'); ?></input><br/>
                                <input type="radio" <?php EZP_IBC_U::echo_checked($global->ajax_protocol == EZP_IBC_Ajax_Protocols::Https); ?> name="ajax_protocol" value="<?php echo EZP_IBC_Ajax_Protocols::Https; ?>"><?php EZP_IBC_U::_e('HTTPS'); ?></input>
                            </div
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