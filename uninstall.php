<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(defined('WP_UNINSTALL_PLUGIN')) {    

    global $wpdb;

    $main_option = get_option('easy-pie-ibc-options');
    
    $drop_tables = false;
    
    if (array_key_exists('drop-tables-on-uninstall', $main_option))
    {
        error_log('drop tables array key exists');
        error_log(print_r($main_option, true));
        $drop_tables = (bool)$main_option['drop-tables-on-uninstall'];
    }
    else
    {
        error_log('drop tables array key doesnt exist');
    }
    
    if($drop_tables)
    {
        $contacts_table_name = $wpdb->prefix . 'easy_pie_ibc_contacts';
        $events_table_name = $wpdb->prefix . 'easy_pie_ibc_events';
        $contact_ids_table_name = $wpdb->prefix . 'easy_pie_ibc_public_ids';
        $entities_table_name = $wpdb->prefix . 'easy_pie_ibc_entities';

        $wpdb->query("DROP TABLE IF EXISTS $contacts_table_name");
        $wpdb->query("DROP TABLE IF EXISTS $events_table_name");
        $wpdb->query("DROP TABLE IF EXISTS $contact_ids_table_name");
        $wpdb->query("DROP TABLE IF EXISTS $entities_table_name");             
        
        error_log('dropping tables');
    }
    else
    {
        error_log('not dropping tables');
    }
    
    delete_option('easy-pie-ibc-options');
}
?>
