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
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-report-helper.php');

if (!class_exists('EZP_IBC_List_Member_Control'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_List_Member_Control extends WP_List_Table
    {
        private $list_id = -1;
        private $nonce_action = null;

        public function __construct($list_id, $nonce_action)
        {
            $this->list_id = $list_id;            
            $this->nonce_action = $nonce_action;
            
            parent::__construct();
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
                case 'display_name':
                case 'email':
                case 'stage':
                case 'creation_timestamp':                
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

            $this->process_bulk_action();

            $data = $this->get_table_data();
            usort($data, array(&$this, 'sort_data'));

            $perPage = $this->get_items_per_page('list_members_per_page', 10);
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
   
        public function process_bulk_action()
        {
            // RSR TODO
            // security check!
//            if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
//
//                $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
//                $action = 'bulk-' . $this->_args['plural'];
//
//                if (!wp_verify_nonce($nonce, $action))
//                    wp_die('Nope! Security check failed!');
//            }

            $action = $this->current_action();

            switch ($action)
            {
                case 'remove':
                    if (isset($_POST['member_id']))
                    {
                        $member_ids = $_POST['member_id'];

               //         EZP_IBC_U::debug_object('selected members', $member_ids);

                        foreach ($member_ids as $member_id)
                        {
                            EZP_IBC_U::debug("removing member $member_id from list $this->list_id");
                            
                            $member = EZP_IBC_Contact_Entity::get_by_id($member_id);
                            
                            if($member != null)
                            {
                                $member->remove_from_list($this->list_id);
                                $member->save();
                            }
                            else
                            {
                                // rsr todo error handling
                            }
                        }
                    }

                    break;

                default:
                    // do nothing or something else
                    return;
                    break;
            }

            return;
        }

        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                //'id' => EZP_IBC_U::__('ID'),
                'display_name' => EZP_IBC_U::__('Name'),
                'stage' => EZP_IBC_U::__('Stage'),
                'email' => EZP_IBC_U::__('Email'),
                    //'creation_timestamp' => EZP_IBC_U::__('Created')
            );

            return $columns;
        }

        public function get_hidden_columns()
        {
            return array('id');
        }

        public function get_sortable_columns()
        {
            return array('display_name' => array('display_name', false));
        }

        public function get_bulk_actions()
        {

            $actions = array(
                'remove' => 'Remove'
            );

            return $actions;
        }

        private function get_table_data()
        {
            global $wpdb;            
                       
            $members = EZP_IBC_Contact_Entity::get_all_by_list_id($this->list_id);
                          
            $data = array();

            foreach ($members as $member)
            {                
                $data[] = (array) $member;
            }

            return $data;
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

        function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="member_id[]" value="%s" />', $item['id']);
        }

        // <editor-fold desc="Column display functions">

        function column_display_name($item)
        {
            $edit_url = sprintf('?page=%s&contact_id=%d', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, $item['id']);
            $edit_anchor = sprintf("<a href='$edit_url'>%s</a>", EZP_IBC_U::__('Edit'));

            //     EZP_IBC_U::debug("edit anchor $edit_anchor");
            // rsr todo see if more elegant way of doing this without getting page
            $current_page = $_GET['page'];

            //RSR TODO BAD - don't do a delete in a GET!!!
            // RSR Put in a protection into the delete function - both prompting and nonce checking
            //$delete_anchor = sprintf('<a href="?page=%s&list_id=%d&member_id=%d&action=remove">Remove</a>', $current_page, $this->list_id, $item['id']);

            $remove_url = menu_page_url(EZP_IBC_Constants::$LIST_SUBMENU_SLUG, false);
            
            $remove_url = EZP_IBC_U::append_query_value($remove_url, 'list_id', $this->list_id);
            $remove_url = EZP_IBC_U::append_query_value($remove_url, 'member_id', $item['id']);
            $remove_url = EZP_IBC_U::append_query_value($remove_url, 'form_action', 'remove');
            $remove_url = EZP_IBC_U::append_query_value($remove_url, 'tab', 'members');

            $remove_url = wp_nonce_url($remove_url, $this->nonce_action);
            
            $remove_anchor = "<a href='$remove_url'>" . __('Remove') . '</a>';
            
            
            $actions = array(
                'edit' => $edit_anchor,
                'remove' => $remove_anchor,
            );

            $contact = EZP_IBC_Contact_Entity::get_by_id((int)$item['id']);
            
            $display_name = $contact->get_display_name();
            
            if ($item['wpid'] != -1)
            {
                $display_name = "$display_name (WP)";
            }

            $title = EZP_IBC_U::__('Edit') . ' ' . $display_name;
            return sprintf("<a title='$title' href='$edit_url'>%1\$s</a> %2\$s", $display_name, $this->row_actions($actions));
        }

        function column_stage($item)
        {

            return EZP_IBC_Contact_Entity::get_stage_string($item['stage']);
        }
        // </editor-fold>
    }
}