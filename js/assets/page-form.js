/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

easyPie = {};
easyPie.IBC = {};

//easyPie.IBC.fieldBoxInstance = 0;

easyPie.IBC.htmlEntities = function (str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
};

easyPie.IBC.addNewFieldBox = function () {    

    var dec = jQuery('<div/>').html(window.easyPieFieldBoxTemplate).text();

    dec = dec.split("{{instance}}").join(easyPie.IBC.fieldBoxInstance);
    dec = dec.split("{{form_field_id}}").join('-1');

    dec = "<li>" + dec + "</li>";

    jQuery("#easy-pie-ibc-field-holder").append(dec);
    
    easyPie.IBC.fieldBoxInstance++;
};

easyPie.IBC.removeFieldBox = function (fieldboxInstance) {
    var fieldBoxSelector = "#easy-pie-fieldbox-" + fieldboxInstance;
    
    jQuery(fieldBoxSelector).parent().remove();
}

easyPie.IBC.TogglePostbox = function (e) {

    // alert('toggle');
    //  var selector = "#" + element_id;

//    jQuery(e).toggleClass("closed");

    jQuery(e).parent().siblings(".easy-pie-postbox-body").toggle();
};

easyPie.IBC.updateFieldboxTitle = function (fieldboxInstance) {

    var titleSelector = "#easy-pie-fieldbox-title-" + fieldboxInstance;
    var labelSelector = "#easy-pie-fieldbox-label-" + fieldboxInstance;

    var titleString = "- " + jQuery(labelSelector).val();

    jQuery(titleSelector).text(titleString);
};

easyPie.IBC.selectTemplate = function (templateType) {

    var selectedSelector = "img[template_type='" + templateType + "']";
    var inputSelector = "input[name='template_id']";
    var selectedBorder = "solid black 1px";

    jQuery(selectedSelector).css({border: selectedBorder});
    jQuery(inputSelector).val(templateType);
    for (n = -3; n <= -1; n++)
    {
        if (n !== templateType)
        {
            var unselectedSelector = "img[template_type='" + n + "']";

            jQuery(unselectedSelector).css({border: ""});
        }
    }
}



easyPie.IBC.drop = function (event, ui) {

    jQuery("#easy-pie-ibc-field-holder li").each(function (i, el) {

        jQuery(this).find("input[name='field_order[]']").val(i);
    });
}

easyPie.IBC.ChangeFieldSelectorVisibility = function (showFieldSelector, index) {

    // alert('toggle');
    //  var selector = "#" + element_id;

//    jQuery(e).toggleClass("closed");

    var selector = "#ezp-form-contact-field-selector-" + index;

    if(showFieldSelector) {
    //    jQuery("#ezp-form-contact-field-selector").slideDown();
        jQuery(selector).show();
    }
    else {
        //jQuery("#ezp-form-contact-field-selector").slideUp();
        jQuery(selector).hide();
    }
};

jQuery(document).ready(function ($) {
//    $(".spectrum-picker").spectrum({
//        preferredFormat: "hex",
//        show: function (color) {
//            console.log(color.toHexString());
//        },
//        change: function (color) {
//            $(this).val(color);
//            console.log(color.toHexString());
//        },
//        showInput: true,
//        theme: "sp-light"
//    });

    $("#easy-pie-ibc-field-holder").sortable({stop: easyPie.IBC.drop});

    easyPie.IBC.selectTemplate(Number($("input[name='template_id']").val()))
});