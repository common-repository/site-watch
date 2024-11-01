/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function ($) {
            
    submenuSelector = "#toplevel_page_site-watch";
    
    jQuery(submenuSelector).addClass("current wp-has-current-submenu wp-menu-open");
    
    jQuery(submenuSelector).find("ul li:nth-child(3)").addClass("current");
});