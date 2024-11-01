/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

easyPie.createNS("easyPie.IBC.Support.PageSupport");

easyPie.IBC.Support.PageSupport.getDebugFile = function () {
    
    var actionLocation = ajaxurl + '?action=EZP_IBC_get_debug_file';
    
    location.href = actionLocation;
}