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

<script type="text/javascript">
    window.easyPieFieldBoxTemplate = "<?php
//$template_string = htmlentities(file_get_contents(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/fieldbox-template.html'));

$fieldbox = EZP_IBC_U::get_php_file_output(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/fieldbox-template.php');

$template_string = htmlentities($fieldbox);
echo trim(preg_replace('/\s+/', ' ', $template_string));
?>";
</script>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/assets/page-form.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style>
    .easy-pie-form-template { border: solid lightgray 1px;} 
</style>

<?php
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-form-entity.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-form-template-entity.php');

$action_updated = null;

$form_id = EZP_IBC_U::get_request_val('form_id', -1);

$creating_form = (bool) ($form_id == -1);

if ($creating_form)
{
    $form = new EZP_IBC_Form_Entity();
}
else
{
    $form = EZP_IBC_Form_Entity::get_by_id($form_id);
}

$error_string = "";

if (isset($_POST['action']))
{
    check_admin_referer('easy-pie-ibc-save-contact');

    $action = $_POST['action'];

    if ($action == 'save')
    {
        // Artificially set the bools since they aren't part of the postback
        //    $display->background_tiling_enabled = "false";

        $error_string = $form->save_from_admin_post($_POST);
               
        if ($error_string == true)
        {
            $error_string = "";
            // May be false or a string with errors with 
            $action_updated =  true;
        } 
        else
        {
            // its either a false or an error
        }

        EZP_IBC_U::debug("action saved, action updated=$action_updated");
    }
}
?>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">
    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2><?php echo $creating_form == true ? EZP_IBC_U::__('Create Form') : EZP_IBC_U::__('Edit Form') . ' - Site Spy'; ?></h2>

    <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$FORM_SUBMENU_SLUG); ?>" >             
        <?php wp_nonce_field('easy-pie-ibc-save-contact'); ?>
        <input type="hidden" name="action" value="save"/>
        <?php
        if ($error_string != "") :
            ?>
            <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
        <?php endif; ?>

        <?php if ($action_updated) : ?>
            <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Form Updated.'); ?></div>
        <?php endif; ?>

        <span><?php            
            echo '[ezp-form id=';
            echo  $form->id == -1 ? EZP_IBC_U::__('N/A') : $form->id . ']';
            ?></span>                        
        <input type="hidden" name="form_id" value="<?php echo $form->id; ?>"/>

        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >

                <table class="form-table">                                        
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Name") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="name" type="text" value="<?php echo $form->name; ?>" />                        
                            </div>
                        </td>
                    </tr>               
                </table>
            </div>
        </div>
        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >

                <h3><?php EZP_IBC_U::_e("Settings") ?></h3>

                <table class="form-table">                                        
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Template") ?></span>
                        </th>
                        <td>
                            <div>
                                <img template_type="<?php echo EZP_IBC_Built_In_Form_Template_ID::OneBy ?>" class="easy-pie-form-template" onclick="easyPie.IBC.selectTemplate(<?php echo EZP_IBC_Built_In_Form_Template_ID::OneBy; ?>)" style="float:left; margin-right:15px" src="<?php echo EZP_IBC_U::$PLUGIN_URL . '/images/form-magnet1.png' ?>"/>
                            </div>
                            <div>
                                <img template_type="<?php echo EZP_IBC_Built_In_Form_Template_ID::TwoBy ?>" class="easy-pie-form-template" onclick="easyPie.IBC.selectTemplate(<?php echo EZP_IBC_Built_In_Form_Template_ID::TwoBy; ?>)" style="float:left; margin-right:15px"  src="<?php echo EZP_IBC_U::$PLUGIN_URL . '/images/form-magnet2.png' ?>"/>
                            </div>
                        </td>
                        <input type="hidden" name="template_id" value="<?php echo $form->template_id; ?>" />
                    </tr>               
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Success Message") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="post_success_message" type="text" value="<?php echo $form->post_success_message; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                    
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Hide After Submit") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="hide_submitted_form" type="checkbox" value="<?php echo $form->hide_submitted_form; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                    
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Clear After Submit") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="clear_submitted_form" type="checkbox" value="<?php echo $form->clear_submitted_form; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                    
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Success URL") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="success_url" type="text" value="<?php echo $form->success_url; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                </table>
            </div>
        </div>


        <div class="postbox" >
            <div class="inside" >
                <h3><?php EZP_IBC_U::_e("Fields") ?></h3>

                <ol class="postbox-container" id="easy-pie-ibc-field-holder" style="float:none; width:600px">
                    <?php
                        $instance = 0;
                        
                        // Render existing field boxes
                        if($form->id != -1)
                        {                            
                            $db_form_fields = EZP_IBC_Form_Field_Entity::get_all_by_form_id($form->id); // rsr should be in order of field_order
                                                            
                            
                                                        
                            foreach($db_form_fields as $db_form_field)
                            {  
                                
                                $form_field_entity = $db_form_field;
                                ob_start();
                                include(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/fieldbox-template.php');
                                $fieldbox_template = ob_get_clean();
            
                                /* @var $db_form_field EZP_IBC_Form_Field_Entity */
                                $fieldbox_instance = str_replace('{{instance}}', "$instance", $fieldbox_template);
                                $fieldbox_instance = str_replace('{{form_field_id}}', $db_form_field->id, $fieldbox_instance);
                                
                                
                                echo "<li><div>$fieldbox_instance</div></li>";
                                
                                $instance++;
                            }
                        }
                        echo "<script ='text/javascript>easyPie.IBC.fieldBoxInstance = $instance;</script>";
                    ?>
                </ol>
            </div>
            <button type="button" onclick="easyPie.IBC.addNewFieldBox();
                    return false;"><?php EZP_IBC_U::_e('Add Field') ?></button>
        </div>

        <p>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $creating_form ? _e('Save') : _e('Update'); ?>" />
            <input style="margin-left:12px" type="submit" name="submit" id="submit" class="button button-primary" value="Preview" onclick="return false;"/>
        </p>                

        <div>
            <?php EZP_IBC_U::echo_footer_links(); ?>
        </div>
    </form>
</div>

