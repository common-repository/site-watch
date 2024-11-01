/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
easyPie.createNS("easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab");


//easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue = [];
//easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityIndex = -1;

easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.displayDetails = function(title, details) {
    
    jQuery("#easy-pie-realtime-monitor-detail-dialog").attr("title", title);
    jQuery("#easy-pie-realtime-monitor-detail-dialog").html(details);
    
    jQuery("#easy-pie-realtime-monitor-detail-dialog").dialog({
        modal: true,
        resizable: false,
        width: 400
    });
}


easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.updateLatestActivityDisplay = function (activityData) {
//visitor_count, latest_events, active_contacts

    jQuery('#easy-pie-visitor-count').text(' (' + activityData.visitor_count + ')');


    /*-- Active contacts --*/

    if (activityData.active_contacts.length > 0) {

        rowValues = [];

        rowIndex = 0;

        rowValues[++rowIndex] = '<tr><th style="width:60px">';
        rowValues[++rowIndex] = 'Idle Time';
        rowValues[++rowIndex] = '</th><th style="width:130px">';
        rowValues[++rowIndex] = 'Name';
        rowValues[++rowIndex] = '</th><th style="width:54px">';
        rowValues[++rowIndex] = 'Score';
        rowValues[++rowIndex] = '</th><th style="width:137px">';
        rowValues[++rowIndex] = 'Host';
        rowValues[++rowIndex] = '</th><th>';
        rowValues[++rowIndex] = 'Last Event';
        rowValues[++rowIndex] = '</th>';


        for (var row = 0, size = activityData.active_contacts.length; row < size; row++)
        {
            rowValues[++rowIndex] = '<tr><td style="width:70px">';
            rowValues[++rowIndex] = activityData.active_contacts[row]['time_delta_in_sec'];
            rowValues[++rowIndex] = '</td><td style="width:130px">';
            rowValues[++rowIndex] = activityData.active_contacts[row]['display_name'];
            rowValues[++rowIndex] = '</td><td>';
            rowValues[++rowIndex] = activityData.active_contacts[row]['score'];
            rowValues[++rowIndex] = '</td><td>';
            rowValues[++rowIndex] = activityData.active_contacts[row]['last_hostname'];
            rowValues[++rowIndex] = '</td><td>';
            rowValues[++rowIndex] = activityData.active_contacts[row]['last_event_description'];
            rowValues[++rowIndex] = '</td></tr>';
        }

        jQuery('#easy-pie-latest-visitors').html(rowValues.join(''));
    }
    else
    {
        jQuery('#easy-pie-latest-visitors').html('<tr><td>No visitors in last 5 minutes.</td></tr>');
    }

    /*-- Events --*/
    if (activityData.latest_events.length > 0) {

        rowValues = [];

        rowIndex = 0;
        for (var row = 0, size = activityData.latest_events.length; row < size; row++)
        {
            rowValues[++rowIndex] = '<tr><td>';
            rowValues[++rowIndex] = activityData.latest_events[row]['text'];
            rowValues[++rowIndex] = '</td></tr>';
        }

        jQuery('#easy-pie-ibc-latest-events').html(rowValues.join(''));
    }
    else
    {
        jQuery('#easy-pie-ibc-latest-events').html('<tr><td>No activity in last 5 minutes.</td></tr>');
    }

//    if (activityData.latest_events.length > 0) {
//
//        easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityIndex = activityData.latest_events[0]['index']; // [0] is the newest record
//
//        easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue = activityData.latest_events.concat(easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue);
//
//        while (easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue.length > 10)
//        {
//            easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue.pop();
//        }
//
//        rowValues = [];
//
//        rowIndex = 0;
//        for (var row = 0, size = easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue.length; row < size; row++)
//        {
//            rowValues[++rowIndex] = '<tr><td>';
//            rowValues[++rowIndex] = easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityQueue[row]['text'];
//            rowValues[++rowIndex] = '</td></tr>';
//        }
//
//        jQuery('#easy-pie-ibc-latest-events').html(rowValues.join(''));
//    }
}

easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.eventActivityPlaying = true;
easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.togglePlay = function (shouldPlay)
{
    easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.eventActivityPlaying = shouldPlay;


    var color = ''
    var text = '';

    if (shouldPlay)
    {
        jQuery('#easy-pie-play-icon').css('color', 'green');
        jQuery('#easy-pie-pause-icon').css('color', '');
    }
    else
    {
        jQuery('#easy-pie-pause-icon').css('color', 'darkred');
        jQuery('#easy-pie-play-icon').css('color', '');

        text = "<span style='color:darkred'>(Paused)</span>";
    }
    jQuery('#easy-pie-realtime-status').html(text);






}

easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.getLatestActivity = function () {

    // rsr to do call EZP_IBC_get_recent_activity

    if (easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.eventActivityPlaying)
    {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            timeout: 10000000,
            data:
                    {
                        'action': 'EZP_IBC_get_recent_activity',
                        // 'index': easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.activityIndex
                        //  '_wpnonce' : wpnonce rsr todo
                    },
            beforeSend: function () {
                //   alert('beforesend');
            },
            complete: function () {
                //    alert('complete');
            },
            success: function (data) {
                easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.updateLatestActivityDisplay(data);
            },
            error: function (data) {
                //        alert('track error' + data);
                console.log('recent activity error:' + data);
                //  alert('error');                        
                //  location.reload();
            }
        });
    }
}



jQuery(document).ready(function ($) {

    easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.togglePlay(true);
    window.setInterval(easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.getLatestActivity, 4000);
    easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.getLatestActivity();
});

