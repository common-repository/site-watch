/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



//var ns = 
easyPie.createNS("easyPie.IBC.People.PagePeople.ContactsTab");

easyPie.IBC.People.PagePeople.ContactsTab.exportContacts = function (stage, event, event_parameter, event_range) {
    
    var actionLocation = ajaxurl + '?action=EZP_IBC_export_contacts&stage=' + stage + '&event=' + event + '&event_parameter=' + event_parameter + '&event_range=' + event_range;
    
    location.href = actionLocation;
}

//easyPie.IBC.Contacts.PageContacts.changeDateFilterState = function (selectedIndex) {
//    
//    if(selectedIndex == 0)
//    {
//     //   jQuery('#ezp_ibc_event_range_filter').prop('disabled', true);
//        jQuery('#ezp_ibc_event_range_filter').hide();
//       // jQuery('#ezp_ibc_event_range_filter').val(-1)
//    }
//    else
//    {        
//        //jQuery('#ezp_ibc_event_range_filter').prop('disabled', false);
//        jQuery('#ezp_ibc_event_range_filter').show();
//    }
//}
//ezp-ibc-list-select  addContactsToList
easyPie.IBC.People.PagePeople.ContactsTab.addContactsToList = function (stage, event, event_parameter, event_range) {
    
    var listID = jQuery('#ezp-ibc-list-select').val();
    
    //alert('todo make ajax request with ' + listID + "," + stage + "," + event + "," + event_parameter + "," + event_range);
    
//    var actionLocation = ajaxurl + '?action=EZP_IBC_export_contacts&stage=' + stage + '&event=' + event + '&event_parameter=' + event_parameter + '&event_range=' + event_range;
    
  //  location.href = actionLocation;
  
  jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: "json",
        timeout: 10000000,
        data:
                {
                    'action': 'EZP_IBC_add_contacts_to_list',
                    'list_id': listID,
                    'stage': stage,
                    'event': event,
                    'event_parameter': event_parameter,
                    'event_range': event_range,
                            //  '_wpnonce' : wpnonce
                },
        beforeSend: function () {
            //   alert('beforesend');
        },
        complete: function () {
            //    alert('complete');
        },
        success: function (data) {
            alert('add to list success ' + data);
            //  alert('succeess');
            //            successCallback();
            //            location.reload();
        },
        error: function (data) {
            alert('add to list error' + data);
            console.log('Tracking error:' + data);
            //  alert('error');                        
            //  location.reload();
        }
    });
}

easyPie.IBC.People.PagePeople.ContactsTab.showAdvancedFilter = function(show){
    
    if(show)
    {
        jQuery('#ezp_ibc_event_filter').show();
        jQuery('#ezp_ibc_event_range_filter').show();        
    }
    else
    {
        jQuery('#ezp_ibc_event_filter').hide();
        jQuery('#ezp_ibc_event_range_filter').hide();
        jQuery("#ezp_ibc_event_filter").val(-1);
        jQuery("#ezp_ibc_event_range_filter").val(0);
    }
    
    
}

jQuery(document).ready(function ($) {
 //   $("#easy-pie-delete-confirm").dialog();
});