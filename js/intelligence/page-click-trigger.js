/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//easyPie.IBC.fieldBoxInstance = 0;
//
//easyPie.IBC.htmlEntities = function (str) {
//    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
//};

easyPie.createNS("easyPie.IBC.UserEvents.PageClickTrigger");


easyPie.IBC.UserEvents.PageClickTrigger.showCreateUserEventTypeRadio = function ()
{
    jQuery("#easy-pie-ibc-new-user-event-type-form").dialog( { 
        modal: true,
        resizable: false,
        width: 400
    });
}

jQuery(document).ready(function ($) {
            
    submenuSelector = "#toplevel_page_site-watch";
    
    jQuery(submenuSelector).addClass("current wp-has-current-submenu wp-menu-open");
    
    jQuery(submenuSelector).find("ul li:nth-child(4)").addClass("current");
});