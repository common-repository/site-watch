<?php
if (isset($form_field_entity) == false)
{
    $form_field_entity = new EZP_IBC_Form_Field_Entity();
}
/* @var $form_field_entity EZP_IBC_Form_Field_Entity */

$title_label = $form_field_entity->label == '' ? EZP_IBC_U::__('No Label') : $form_field_entity->label;
$title = "$title_label (" . EZP_IBC_Contact_Entity::get_field_display_name($form_field_entity->contact_field_id) . ')';
?>
<div id="easy-pie-fieldbox-{{instance}}"  style="display: inline-block; vertical-align: middle;width:600px">
    <style>        
        .easy-pie-postbox { border: solid lightgray 1px;}
        .easy-pie-postbox:hover { border: solid black 1px;}
        .easy-pie-postbox-title { border: solid lightgray 1px; }
    </style>
    <div class="easy-pie-postbox" style="margin:5px 5px 5px 0">
        <div class="easy-pie-postbox-title" style="background:#fafafa; height:50px; cursor:move;">
            <h3 id="easy-pie-fieldbox-title-{{instance}}" style="padding-left:10px; float:left;"><?php echo $title ?></h3>
            <div title="Click to toggle" onclick="easyPie.IBC.TogglePostbox(this);" style="width:20px; padding-top:18px;padding-right:5px; float:right;cursor:pointer">+</div>
        </div>        
        <div class="easy-pie-postbox-body" style="background:white; display:none;">
            <div style="margin-top:5px; padding-left:10px;">                
                <table class="form-table">   
                    <input type="hidden" value="{{form_field_id}}" name="form_field_id[]"/>
                    <input type="hidden" name="field_order[]" />
<!--                    <input type="hidden" name="post_field_name[]" />-->
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Field Type") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">  
                                <input type="radio" <?php echo $form_field_entity->type == EZP_IBC_Form_Field_Types::Contact_Field ? 'checked' : ''; ?> onclick="easyPie.IBC.ChangeFieldSelectorVisibility(true, {{instance}});" name="form_field_type[{{instance}}]" value="<?php echo EZP_IBC_Form_Field_Types::Contact_Field; ?>"><?php EZP_IBC_U::_e('Contact') ?></input>
                                <input style='margin-left:12px;' <?php echo $form_field_entity->type == EZP_IBC_Form_Field_Types::Data_Field ? 'checked' : ''; ?>  type="radio" onclick="easyPie.IBC.ChangeFieldSelectorVisibility(false, {{instance}});" name="form_field_type[{{instance}}]" value="<?php echo EZP_IBC_Form_Field_Types::Data_Field; ?>"><?php EZP_IBC_U::_e('Data') ?></input>
                            </div>
                        </td>
                    </tr>   
                    <tr id="ezp-form-contact-field-selector-{{instance}}">

                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Contact Field") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <select name="contact_field_id[]" value="<?php echo $form_field_entity->contact_field_id ?>">      
                                    <?php
                                    $mappings = EZP_IBC_Contact_Entity::get_field_display_name_mapping();

                                    foreach ($mappings as $index => $display_name)
                                    {
                                        if ($index == $form_field_entity->contact_field_id)
                                        {
                                            $selected = "selected";
                                        }
                                        else
                                        {
                                            $selected = "";
                                        }
                                        echo "<option $selected value='$index'>$display_name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>                    
                    </tr>   
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Label") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input id="easy-pie-fieldbox-label-{{instance}}" onkeyup="easyPie.IBC.updateFieldboxTitle('{{instance}}');" name="label[]" type="text" value="<?php echo $form_field_entity->label; ?>" />                        
                            </div>
                        </td>
                    </tr>   
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Description") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input name="description[]" type="text" value="<?php echo $form_field_entity->description; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Default Value") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="default_value[]" type="text" value="<?php echo $form_field_entity->default_value; ?>" />                        
                            </div>
                        </td>
                    </tr> 
                    <tr>
                        <th scope="row">
                            <span><?php echo EZP_IBC_U::_e("Required") ?></span>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input  name="required[]" type="checkbox" value="<?php echo $form_field_entity->required; ?>" <?php EZP_IBC_U::echo_checked($form_field_entity->required); ?>  />                        
                            </div>
                        </td>
                    </tr> 
                    <tr>
                        <th scope="row">
                        </th>
                        <td>
                            <button type="button" onclick="easyPie.IBC.removeFieldBox('{{instance}}');" style="float:right;"><?php EZP_IBC_U::_e('Remove'); ?></button>
                        </td>
                    </tr> 
                </table>
            </div>        
        </div>
    </div>
</div>
