/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

easyPie.createNS("easyPie.IBC.Settings.PageSettings.StorageTab");

easyPie.IBC.Settings.PageSettings.StorageTab.purgeAllRecords = function () {
    
    var purge = confirm("Click OK to delete ALL contact and event records.");
    
    if(purge)
    {
        jQuery("[name=action]").val("purge_all_records");
        return true;
    }
    else
    {
        // cancelled
        return false;
    }
}