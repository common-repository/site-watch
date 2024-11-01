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
/* @var $global EZP_IBC_Global_Entity */

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

        $scores_recalculated = false;
        
        $error_string = '';

        if (isset($_POST['action']))
        {
            $action = $_POST['action'];
            
            if($action == 'save') 
            {            
                $error_string = $global->set_post_variables($_POST);

                if ($error_string == '') 
                {
                    $global->save();
                    $action_updated = __('Settings Saved');
                }
            } 
            else if($action == 'recalculate_scores')
            {
                $error_string = $global->set_post_variables($_POST);

                // First save what was in the form
                if ($error_string == '') 
                {
                    $global->save();
                    $action_updated = __('Settings Saved and Scores Recalculated.');
                    
                    EZP_IBC_Contact_Entity::score_all();
                    $scores_recalculated = true;
                }                                
            }
        }
        ?>

        <input type="hidden" name="action" value="save"/>            
        <?php
        if ($error_string != '') :
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php endif; ?>

        <?php if (!empty($action_updated)) : ?>
            <div id="message" class="updated below-h2"><p><span><?php echo $action_updated; ?></span></p></div>
        <?php endif; ?>       
         
        <div class="postbox" >
            <div class="inside" > 
                <h3><?php _e("Event Worths") ?></h3>
                <table class="form-table">                                  
                    <tr>
                        <th scope="row">
                            <?php echo _e("Form Submit") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="narrow-input" name="form_submit_event_worth" type="text" value="<?php echo $global->form_submit_event_worth; ?>" />
                            </div>
                        </td>
                    </tr>  
                    
                    <tr>
                        <th scope="row">
                            <?php echo _e("Login") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="narrow-input" name="login_event_worth" type="text" value="<?php echo $global->login_event_worth; ?>" />
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php echo _e("Page View") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="narrow-input" name="page_view_event_worth" type="text" value="<?php echo $global->page_view_event_worth; ?>" />
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php echo _e("Purchase") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input class="narrow-input" name="purchase_event_worth" type="text" value="<?php echo $global->purchase_event_worth; ?>" />
                            </div>
                        </td>
                    </tr>
                    
                </table>
            </div>
        </div>
            
        <div class="postbox" >
            <div class="inside" > 
                <h3><?php _e("Calculation") ?></h3>
                <table class="form-table">                                  
                    <tr>
                        <th scope="row">
                            <?php echo _e("Scores") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <button type="submit" onclick="jQuery('[name=action]').val('recalculate_scores'); return true;"><?php _e('Recalculate All'); ?></button>
                                <div class="description-div"><span class="description"><?php echo __('Recalculate contact scores using current event worths. <strong>Purged events disregarded.</strong>') ?></span></div>
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