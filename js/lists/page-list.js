//rsr todo old list management logic
/////* 
// * To change this license header, choose License Headers in Project Properties.
// * To change this template file, choose Tools | Templates
// * and open the template in the editor.
// */
//
//
////var ns = 
//easyPie.createNS("easyPie.IBC.Lists.PageList");
//
//easyPie.IBC.Lists.PageList.getContacts = function (filter, successCallback, failureCallback) {
//
//    //   alert('getting contacts');
//    jQuery.ajax({
//        type: "POST",
//        url: ajaxurl,
//        dataType: "json",
//        timeout: 10000000,
//        data:
//                {
//                    'action': 'EZP_IBC_get_contacts',
//                    'filter': filter,
//                    //  '_wpnonce' : wpnonce
//                },
//        beforeSend: function () {
//            //   alert('beforesend');
//        },
//        complete: function () {
//            //    alert('complete');
//        },
//        success: function (data) {
//            //      alert('get contact success ' + data);            
//            successCallback(data);
//            //            location.reload();
//        },
//        error: function (data) {
//            //     alert('get contact error' + data);
//            //  alert('error');                        
//            //  location.reload();
//        }
//    });
//}
//
//easyPie.IBC.Lists.PageList.changeMembership = function (addMembers) {
//
//    var recordIDs = [];
//    var sourceOptionSelector = "";
//
//    if (addMembers) {
//
//        sourceOptionSelector = "#ezp-ibc-non-member-list option:selected"
//
//    } else {
//
//        sourceOptionSelector = "#ezp-ibc-member-list option:selected"
//    }
//
//    jQuery(sourceOptionSelector).each(function () {
//
//        var recordID = jQuery(this).val();
//
//        recordIDs.push(recordID);
//    });
//
//    easyPie.IBC.Lists.PageList.switchMembership(recordIDs, addMembers)
//}
//
//easyPie.IBC.Lists.PageList.switchMembership = function (recordIDs, addMembers)
//{
//    var baseSourceRecordSelector;
//    var destListSelector;
//
//    if (addMembers)
//    {
//        baseSourceRecordSelector = "#ezp-ibc-non-member-list";
//        destListSelector = "#ezp-ibc-member-list";
//    }
//    else
//    {
//        baseSourceRecordSelector = "#ezp-ibc-member-list";
//        destListSelector = "#ezp-ibc-non-member-list";
//    }
//
//    for (var key in recordIDs)
//    {
//        var recordID = recordIDs[key];
//        var sourceRecordSelector = baseSourceRecordSelector + easyPie.stringFormat(" option[value='{0}']", recordID);
//
//        var record = easyPie.IBC.Lists.PageList.masterRecordList[recordID];
//
//        var displayName = '';
//
//        if (record.email) {
//            displayName = easyPie.stringFormat('{0} ({1})', record.display_name, record.email);
//        }
//        else {
//            displayName = easyPie.stringFormat('{0}', record.display_name);
//        }
//
//        jQuery(destListSelector).append((jQuery("<option></option>")).attr("value", record.id).text(displayName));
//        jQuery(sourceRecordSelector).remove();
//    }
//}
//
//easyPie.IBC.Lists.PageList.postListMembers = function()
//{
//    var members = [];
//    jQuery("#ezp-ibc-member-list option").each(function(index) {
//           
//        members.push(parseInt(jQuery(this).val()));
//    });
//    
//    var jsonMembers = JSON.stringify(members);
//    
//    jQuery("input[name='contact_ids']").val(jsonMembers);
//}
//
//
//jQuery(document)
//  .ajaxStart(function () {
//    jQuery("#ezp-ibc-loading").show();
//  })
//  .ajaxStop(function () {
//    jQuery("#ezp-ibc-loading").hide();
//  });
//  
//


easyPie.createNS("easyPie.IBC.Lists.PageList");

easyPie.IBC.Lists.PageList.exportList = function (list_id) {
    
    var actionLocation = ajaxurl + '?action=EZP_IBC_export_list&list=' + list_id;
    
    location.href = actionLocation;
}

jQuery(document).ready(function ($) {
            
    submenuSelector = "#toplevel_page_site-watch";
    
    jQuery(submenuSelector).addClass("current wp-has-current-submenu wp-menu-open");
    
    jQuery(submenuSelector).find("ul li:nth-child(3)").addClass("current");
});