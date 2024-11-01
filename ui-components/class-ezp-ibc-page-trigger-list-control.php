<?php

/*
  Easy Pie Site Spy Plugin
  Copyright (C) 2015, Synthetic Thought LLC
  website: easypiewp.com contact: bob@easypiewp.com

  Easy Pie Site Spy Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

if (!class_exists('WP_List_Table'))
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Utilities/class-ezp-ibc-u.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-page-trigger-entity.php');

if (!class_exists('EZP_IBC_Page_Trigger_List_Control'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Page_Trigger_List_Control extends WP_List_Table
    {     
        private $nonce_action = null;
        
        public function __construct($nonce_action)
        {
            parent::__construct();
            
            $this->nonce_action = $nonce_action;
        }        
        
        /**
         * Define what data to show on each column of the table
         *
         * @param  Array $item        Data
         * @param  String $column_name - Current column name
         *
         * @return Mixed
         */
        public function column_default($item, $column_name)
        {
            switch ($column_name)
            {
                case 'id':
                case 'post_id':
                case 'pages':
                //case 'post_url':
                case 'user_event_type_name':
                    return $item[$column_name];

                default:
                    return print_r($item, true);
            }
        }

        public function prepare_items()
        {
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();

            $data = $this->get_table_data();
            usort($data, array(&$this, 'sort_data'));

            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($data);

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

            // The following are used inside parent class
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $data;
        }

        public function get_columns()
        {
            $pages_help = __('Hover for page list');
            
            $columns = array(
                'id' => __('Trigger'),                
                'pages' => sprintf(__('Page(s) %1$s'), " <i title='$pages_help' style='color:#aaa' class='fa fa-question-circle'></i>"),
                'user_event_type_name' => EZP_IBC_U::__('User Event')
                
            );

            return $columns;
        }

        public function get_hidden_columns()
        {
            return array('id', 'post_id');            
            //return array();
        }

        public function get_sortable_columns()
        {            
            //return array('name' => array('name', false));            
            return array();
        }       

        private function get_table_data()
        {
            if (isset($_REQUEST['s']))
            {
                $search_filter = $_REQUEST['s'];
            }

          $page_triggers = EZP_IBC_Page_Trigger_Entity::get_all();
                        
            $data = array();                        

            foreach($page_triggers as $page_trigger) 
            {         
                $data[] = (array)$page_trigger;                
            }
        
            return $data;
        }
        
        public function column_pages($item)
        {           
            if($item['trigger_type'] == EZP_IBC_Page_Trigger_Types::URL)
            {
    //            EZP_IBC_U::debug_object('item url', $item['url']);
                
                if(count($item['urls']) > 0)
                {
                    if(count($item['urls']) > 1)
                    {                
                        $title = '';
                        foreach($item['urls'] as $url)
                        {
                            $title .= $url . '&#10;';       
                        }
                                    
                        $text = sprintf(__('<span style="cursor:help" title="%1$s">Several</span>'), $title);        
                    }
                    else
                    {
                        $text = $item['urls'][0];
                    }
                }
                else
                {
                    $text = __('None');                                    
                }
            }
            else
            {               
                $title = '';
                
                $post_title_array = array();
                
                foreach($item['post_ids'] as $post_id)
                {
                    $post_title = get_the_title($post_id);
                    
                    if(empty($post_title))
                    {
                        array_push($post_title_array, $post_id);
                    }
                    else
                    {
                        array_push($post_title_array, $post_title);
                    }
                }
                
                $post_titles =  implode(', ', $post_title_array);
                
                $text = $post_titles;
            }
                  
            $edit_url = sprintf('?page=%1$s&page_trigger_id=%2$d', EZP_IBC_Constants::$PAGE_TRIGGER_SLUG, $item['id']);
            $edit_anchor = sprintf(__('<a href="%1$s">Edit</a>'), $edit_url);
            
            $delete_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
            
            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'page_trigger_id', $item['id']);
            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'tab', 'page-triggers');
            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'action', 'delete');
            
            $delete_url = wp_nonce_url($delete_url, $this->nonce_action);
            
            $delete_anchor = "<a href='$delete_url'>" . __('Delete') . '</a>';
            
            $actions = array(
                'edit' => $edit_anchor,
                'delete' => $delete_anchor,
            );

            return sprintf('<a href="%3$s">%1$s</a> %2$s', $text, $this->row_actions($actions), $edit_url);
        }
        
        public function column_user_event_type_name($item)
        {                        
            $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id($item['user_event_type_id']);
            
            $edit_user_event_anchor = sprintf('<a title="%1$s" href="?page=%2$s&user_event_type_id=%3$d">%4$s</a>', $user_event_type->description, EZP_IBC_Constants::$USER_EVENT_TYPE_SLUG, $user_event_type->id, $user_event_type->name);
            
            //return $user_event_type->name;            
            return $edit_user_event_anchor;
        }

        private function sort_data($a, $b)
        {
            // Set defaults
            $orderby = 'id';
            $order = 'asc';

            // If orderby is set, use this as the sort column
            if (!empty($_GET['orderby']))
            {
                $orderby = $_GET['orderby'];
            }

            // If order is set use this as the order
            if (!empty($_GET['order']))
            {
                $order = $_GET['order'];
            }

            $result = strnatcmp($a[$orderby], $b[$orderby]);

            if ($order === 'asc')
            {
                return $result;
            }

            return -$result;
        }

        // <editor-fold desc="Column display functions">
        
//        function column_id($item)
//        {            
//            $edit_anchor = sprintf('<a href="?page=%s&page_trigger_id=%d">%s</a>', EZP_IBC_Constants::$PAGE_TRIGGER_SLUG, $item['id'], EZP_IBC_U::__('Edit'));
//            
//            $delete_url = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
//            
//            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'page_trigger_id', $item['id']);
//            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'tab', 'page-triggers');
//            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'action', 'delete');
//            
//            $delete_url = wp_nonce_url($delete_url, $this->nonce_action);
//            
//            $delete_anchor = "<a href='$delete_url'>" . __('Delete') . '</a>';
//            
//            $actions = array(
//                'edit' => $edit_anchor,
//                'delete' => $delete_anchor,
//            );
//
//            return sprintf('%1$s %2$s', __('Page Trigger') . ' ' . $item['id'], $this->row_actions($actions));
//        }               
        
        // </editor-fold>
    }
}