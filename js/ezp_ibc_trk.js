/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

easyPie.createNS("easyPie.IBC.tracker");
easyPie.createNS("easyPie.siteWatch");
easyPie.createNS("easyPie.leadWatch");

easyPie.IBC.tracker.sendEvent = function (type, data, parameter, parameter2, parameter3) {

    if (typeof (parameter) === 'undefined')
    {
        parameter = '';
    }

    if (typeof (parameter2) === 'undefined')
    {
        parameter2 = '';
    }

    if (typeof (parameter3) === 'undefined')
    {
        parameter3 = '';
    }

//    data = jQuery.param( data, true );
    // parameter = jQuery.param( parameter, true );
    //   parameter2 = jQuery.param( parameter2, true );
    //  parameter3 = jQuery.param( parameter3, true );

    jQuery.ajax({
        type: "POST",
        url: ezp_ibc_gateway.ajaxurl,
        dataType: "json",
        timeout: 10000000,
        data:
                {
                    'action': 'EZP_IBC_track_event',
                    'type': type,
                    'parameter': parameter,
                    'parameter2': parameter2,
                    'parameter3': parameter3,
                    'data': data,
                    '_wpnonce': ezp_ibc_gateway.nonce
                            //  '_wpnonce' : wpnonce
                },
        beforeSend: function () {
            //   alert('beforesend');
        },
        complete: function () {
            //    alert('complete');
        },
        success: function (data) {
            //         alert('track success ' + data);
            //  alert('succeess');
            //            successCallback();
            //            location.reload();
        },
        error: function (data) {
            //        alert('track error' + data);
            console.log('Tracking error:' + data);
            //  alert('error');                        
            //  location.reload();
        }
    });

}

easyPie.IBC.tracker.eventTypes = {
    Url_Visited: 0,
    Form_Submitted: 1,
    Button_Clicked: 2,
    User_Event: 3
}


easyPie.IBC.tracker.bindClickTriggers = function () {

    function createClickHandler(index) {
        return function () {

            clickTrigger = ezp_ibc_gateway.click_triggers[index];
            var html = jQuery('<div>').append(jQuery(this).clone()).remove().html();

            easyPie.IBC.tracker.sendEvent(easyPie.IBC.tracker.eventTypes.User_Event, html, clickTrigger.user_event_type_id);
        }
    }

    var handlers = [];
    for (var index in ezp_ibc_gateway.click_triggers) {

        handlers[index] = createClickHandler(index);
    }

    for (var index in ezp_ibc_gateway.click_triggers) {

        var clickTrigger = ezp_ibc_gateway.click_triggers[index];

        jQuery(clickTrigger.selector).click(handlers[index]);
    }
}

easyPie.IBC.tracker.bindToForms = function () {

    if (ezp_ibc_gateway.form_capture_mode == 0)
    {
        query = jQuery("form").not(ezp_ibc_gateway.form_capture_list);
    }
    else
    {
        query = jQuery(ezp_ibc_gateway.form_capture_list);
    }

    query.submit(function () {

        var theForm = this;

        var values = {};

        jQuery.each(jQuery(theForm).find("input[type!='hidden']").serializeArray(), function (i, field) {

            var inputElementSelector = "input[name='" + field.name + "']";
            var inputElement = jQuery(theForm).find(inputElementSelector);

            var inputIDAttribute = inputElement.attr('id');

            var label = "";

            if (typeof inputIDAttribute != 'undefined') {

                var labelSelector = "[for='" + inputIDAttribute + "']";
                label = jQuery(theForm).find(labelSelector).first().text();
            }

            var placeholderAttribute = inputElement.attr('placeholder');
            if (typeof placeholderAttribute == 'undefined') {

                placeholderAttribute = '';
            } else {

                placeholderAttribute = placeholderAttribute.trim();
            }

            var fieldname = field.name.replace(/\[/g, "<<<").replace(/\]/g, ">>>");
            var fieldvalue = field.value.replace(/\[/g, "<<<").replace(/\]/g, ">>>");

            if (label == "")
            {
                label = fieldname;
            }
            else
            {
                label = label.replace("*", "");
                label = label.trim();
            }

            values[fieldname] = {label: label, value: fieldvalue, placeholder: placeholderAttribute};
        });

        var id = -1;

        if (jQuery(theForm).is('[id]')) {

            id = "#" + jQuery(theForm).attr('id');
        } else if (jQuery(theForm).is('[name]')) {

            id = jQuery(theForm).attr('name');
        } else if (jQuery(theForm).is('[action]')) {
            id = jQuery(theForm).attr('action');
        }

        easyPie.IBC.tracker.sendEvent(easyPie.IBC.tracker.eventTypes.Form_Submitted, values, id, window.location.href);
    });
}

//easyPie.IBC.tracker.sendEvent = function (type, parameter, data,) {
// form submitted: parameter = form id or name if not exists, data=array of field labels/values

//easyPie.IBC.tracker.sendEvent = function (type, parameter, data) {

easyPie.IBC.tracker.sendUrlEvent = function () {

    //var data = { user_agent: navigator.userAgent };
    easyPie.IBC.tracker.sendEvent(easyPie.IBC.tracker.eventTypes.Url_Visited, '', window.location.href, document.referrer);
}

/* Public Functions */

/* Legacy name */
easyPie.siteWatch.sendUserEvent = function (id) {

    easyPie.leadWatch.sendUserEvent(id);
}

easyPie.leadWatch.sendUserEvent = function (id) {

    easyPie.IBC.tracker.sendEvent(easyPie.IBC.tracker.eventTypes.User_Event, '', id);
}

jQuery(document).ready(function ($) {

    easyPie.IBC.tracker.bindToForms();
    easyPie.IBC.tracker.sendUrlEvent();

    easyPie.IBC.tracker.bindClickTriggers();
});