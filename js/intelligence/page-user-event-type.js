jQuery(document).ready(function ($) {
            
    submenuSelector = "#toplevel_page_site-watch";
    
    jQuery(submenuSelector).addClass("current wp-has-current-submenu wp-menu-open");
    
    jQuery(submenuSelector).find("ul li:nth-child(4)").addClass("current");
});