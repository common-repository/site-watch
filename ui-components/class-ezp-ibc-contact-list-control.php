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

if (!class_exists('EZP_IBC_Contact_List_Control'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Contact_List_Control extends WP_List_Table
    {
        private $stage = -1;
        private $event = -1;
        private $event_parameter = -1;
        private $event_range = -1;
        private $search = null;
        private $query_info = null;
        private $nonce_action = null;
        private $contact_cache = null;

        public function __construct($stage, $event, $event_parameter, $event_range, $search, $query_info, $nonce_action)
        {
            $this->stage = $stage;
            $this->event = $event;
            $this->event_parameter = $event_parameter;
            $this->event_range = $event_range;
            $this->search = $search;
            $this->query_info = $query_info;
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
                case 'creation_timestamp':
                case 'score':
                    //case 'event_count':
                    //case 'list_string':
                    //case 'notes';
                    return $item[$column_name];

                default:
                    return print_r($item, true);
            }
        }

        public function prepare_items()
        {

            apply_filters("debug", "prepare items start");
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();

            $this->process_bulk_action();

            $data = $this->get_table_data();
            usort($data, array(&$this, 'sort_data'));

            $perPage = $this->get_items_per_page('contacts_per_page', 10);
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
            apply_filters("debug", "prepare items end");
        }

        // Overriding the tablenav to prevent the referer from getting out of control
        function display_tablenav($which)
        {
            ?>
            <div class="tablenav <?php echo esc_attr($which); ?>">

                <div class="alignleft actions">
                    <?php $this->bulk_actions(); ?>
                </div>
                <?php
                $this->extra_tablenav($which);
                $this->pagination($which);
                ?>
                <br class="clear" />
            </div>
            <?php
        }

        public function extra_tablenav($which)
        {
            if ($which == 'top')
            {
                apply_filters("debug", "extra table nav start");
                $advanced_display = '';
                $filter_display = '';

                if ($this->query_info != null)
                {
                    $filter_display = 'display:none';
                }

                if ($this->event == -1)
                {
                    $advanced_display = 'display:none';
                }
                ?>


                <div class='alignleft'>                                    
                    <input type="hidden" name="page" value="<?php echo EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG ?>" />
                    <div id="ezp_filters" style="<?php echo $filter_display ?>">

                        <select class='actions' name="stage" id="ezp_ibc_stage_filter">
                            <option value="-1" <?php echo $this->stage == -1 ? 'selected' : '' ?>><?php _e('All Lifecycles'); ?></option>
                            <option value="<?php echo EZP_IBC_Contact_Stages::Prospect ?>" <?php echo $this->stage == EZP_IBC_Contact_Stages::Prospect ? 'selected' : '' ?>><?php _e('Prospects') ?></option>
                            <option value="<?php echo EZP_IBC_Contact_Stages::Lead ?>"  <?php echo $this->stage == EZP_IBC_Contact_Stages::Lead ? 'selected' : '' ?>><?php _e('Leads') ?></option>
                            <option value="<?php echo EZP_IBC_Contact_Stages::Customer ?>"  <?php echo $this->stage == EZP_IBC_Contact_Stages::Customer ? 'selected' : '' ?>><?php _e('Customers') ?></option>
                        </select>
                        <select style='<?php echo $advanced_display ?>' class='actions' name="event" id="ezp_ibc_event_filter" onchange="easyPie.IBC.Contacts.PageContacts.changeDateFilterState(this.selectedIndex);">
                            <option value="-1" <?php echo $this->event == -1 ? 'selected' : '' ?>><?php _e('With Event'); ?></option>

                            <option value="<?php echo EZP_IBC_Event_Types::Form_Submitted ?>" <?php echo $this->event == EZP_IBC_Event_Types::Form_Submitted ? 'selected' : '' ?>><?php _e('Form Submission') ?></option>                                
                            <option value="<?php echo EZP_IBC_Event_Types::Stage_Change ?>" <?php echo $this->event == EZP_IBC_Event_Types::Stage_Change ? 'selected' : '' ?>><?php _e('Lifecycle Change') ?></option>                                
                            <option value="<?php echo EZP_IBC_Event_Types::WP_Login ?>" <?php echo $this->event == EZP_IBC_Event_Types::WP_Login ? 'selected' : '' ?>><?php _e('Login') ?></option>                                
                            <option value="<?php echo EZP_IBC_Event_Types::Purchase ?>" <?php echo $this->event == EZP_IBC_Event_Types::Purchase ? 'selected' : '' ?>><?php _e('Purchase') ?></option>                                                                
                            <option value="<?php echo EZP_IBC_Event_Types::User_Event ?>" <?php echo $this->event == EZP_IBC_Event_Types::User_Event ? 'selected' : '' ?>><?php _e('User Event') ?></option>                                

                            <?php
                            $user_event_types = EZP_IBC_User_Event_Type_Entity::get_all();
                            foreach ($user_event_types as $user_event_type)
                            {
                                /* @var $user_event_type EZP_IBC_User_Event_Type_Entity */

                                $event_type = EZP_IBC_Event_Types::User_Event;
                                $current_encoded_event = $this->event . 'P' . $this->event_parameter;
                                $encoded_event_choice = $event_type . 'P' . $user_event_type->id;

                                $selected_string = $current_encoded_event == $encoded_event_choice ? 'selected' : '';

                                $option_name = __('Triggered') . ' ' . $user_event_type->name;

                                echo "<option value='$encoded_event_choice' $selected_string>$option_name</option>";
                            }
                            ?>
                        </select>
                        <select style='<?php echo $advanced_display ?>' class='actions' name="event_range" id="ezp_ibc_event_range_filter" >
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Disabled ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Disabled ? 'selected' : '' ?>><?php _e('All Time') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Today ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Today ? 'selected' : '' ?>><?php _e('Today') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Yesterday ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Yesterday ? 'selected' : '' ?>><?php _e('Yesterday') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::This_Week ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::This_Week ? 'selected' : '' ?>><?php _e('This Week') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Last_Week ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Last_Week ? 'selected' : '' ?>><?php _e('Last Week') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Last_Month ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Last_Month ? 'selected' : '' ?>><?php _e('Last Month') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Last_7_Days ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Last_7_Days ? 'selected' : '' ?>><?php _e('Last 7 Days') ?></option>
                            <option value="<?php echo EZP_IBC_Report_Range_Types::Last_30_Days ?>" <?php echo $this->event_range == EZP_IBC_Report_Range_Types::Last_30_Days ? 'selected' : '' ?>><?php _e('Last 30 Days') ?></option>
                        </select>
                        <button type="submit" class="button action"><?php _e('Filter'); ?></button>
                        <input <?php EZP_IBC_U::echo_checked($this->event != -1) ?> name="_advanced_filter" type='checkbox' onclick='easyPie.IBC.People.PagePeople.ContactsTab.showAdvancedFilter(this.checked);'><?php _e('Show Advanced Filter') ?></input>
                    </div>
                </div>

                <?php
                apply_filters("debug", "extra table nav end");
            }
        }

        public function process_bulk_action()
        {
            apply_filters("debug", "process bulk start");
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

            if ($action == 'delete')
            {
                if (isset($_REQUEST['contact_id']))
                {
                    $contact_ids = $_REQUEST['contact_id'];

                    foreach ($contact_ids as $contact_id)
                    {
                        EZP_IBC_U::debug("deleting $contact_id");
                        EZP_IBC_Contact_Entity::delete_by_id($contact_id);
                    }
                }
            }
            else if (EZP_IBC_U::starts_with($action, 'listadd-'))
            {
                EZP_IBC_U::debug("list add action $action");
                $list_id = (int) substr($action, 8);

                $contact_ids = $_REQUEST['contact_id'];

                foreach ($contact_ids as $contact_id)
                {
                    EZP_IBC_U::debug("adding $contact_id to list $list_id");

                    $contact = EZP_IBC_Contact_Entity::get_by_id($contact_id);

                    if ($contact != null)
                    {
                        $contact->add_to_list($list_id);
                        $contact->save();
                    }
                }
            }

            apply_filters("debug", "process bulk end");

            return;
        }

        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                //   'id' => __('ID'),                
                'display_name' => __('Name'),
                //'stage' => __('Lifecycle'),                
                'last_activity_timestamp' => ('Last Activity'),
                'score' => __('Score'),
                'list_string' => __('Lists'),
                'notes' => __('Notes')
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
            return array('display_name' => array('display_name', false),
                'last_activity_timestamp' => array('last_activity_timestamp', false),
                'score' => array('score', false));
        }

        public function get_bulk_actions()
        {

            $actions = array();

            $lists = EZP_IBC_List_Entity::get_all();

            foreach ($lists as $list)
            {
                $actions["listadd-$list->id"] = __('Add to ') . $list->name;
            }

            $actions ['delete'] = __('Delete');

            return $actions;
        }

        private function get_table_data()
        {
            apply_filters("debug", "get table data start");
            global $wpdb;

            if ($this->query_info == null)
            {
                // A custom query overrides all else
                $contacts = EZP_IBC_Report_Helper::get_filtered_contacts($this->stage, $this->event, $this->event_parameter, $this->event_range, $this->search);
            }
            else
            {
                $contacts = EZP_IBC_Contact_Entity::get_all_by_custom_query($this->query_info->query);
            }

            $data = array();
            $this->contact_cache = array();

            foreach ($contacts as $contact)
            {

                // $contact->list_string = '';
                //      $contact->event_count = -1;
                $contact->display_name = '';

                $data[] = (array) $contact;

                $this->contact_cache[$contact->id] = $contact;
            }

            apply_filters("debug", "get table data end");
            return $data;
        }

        private function sort_data($a, $b)
        {
            apply_filters("debug", "sort data start");
            // Set defaults
            $orderby = 'last_activity_timestamp';
            $order = 'desc';

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

            apply_filters("debug", "sort data end");

            return -$result;
        }

        function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="contact_id[]" value="%s" />', $item['id']);
        }

        function column_last_activity_timestamp($item)
        {
            apply_filters("debug", "column_last_activity_timestamp start");
            if ($item['last_activity_timestamp'] == null)
            {
                echo _('Unknown');
            }
            else
            {

                apply_filters("debug", "column_last_activity_timestamp end");
                return EZP_IBC_U::get_simplified_local_time_from_formatted_gmt($item['last_activity_timestamp']);
            }
        }

        // <editor-fold desc="Column display functions">

        function column_display_name($item)
        {
            apply_filters("debug", "column display name start");

            $contact_events_url = sprintf('?page=%s&contact_id=%d&tab=events', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, $item['id']);
            $edit_url = sprintf('?page=%s&contact_id=%d&tab=edit', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, $item['id']);
            $edit_anchor = sprintf("<a href='$edit_url'>%s</a>", EZP_IBC_U::__('Edit'));

            $delete_url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);

            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'contact_id', $item['id']);
            $delete_url = EZP_IBC_U::append_query_value($delete_url, 'form-action', 'delete');
            $delete_url = wp_nonce_url($delete_url, $this->nonce_action);

            //   EZP_IBC_U::debug_object('request', $_REQUEST);

            if (isset($_REQUEST['ezpq']))
            {

                $delete_url = EZP_IBC_U::append_query_value($delete_url, 'ezpq', $_REQUEST['ezpq']);
            }
            else
            {
                $delete_url = EZP_IBC_U::append_query_value($delete_url, 'stage', $this->stage);
                $delete_url = EZP_IBC_U::append_query_value($delete_url, 'event', $this->event);
                $delete_url = EZP_IBC_U::append_query_value($delete_url, 'event_parameter', $this->event_parameter);
                $delete_url = EZP_IBC_U::append_query_value($delete_url, 'event_range', $this->event_range);

                if (!empty($this->search))
                {
                    $delete_url = EZP_IBC_U::append_query_value($delete_url, 's', $this->search);
                }
            }



            $delete_anchor = "<a href='$delete_url'>" . __('Delete') . '</a>';

            $actions = array(
                'edit' => $edit_anchor,
                'delete' => $delete_anchor,
            );

            // $contact = EZP_IBC_Contact_Entity::get_by_id((int) $item['id']);
            $contact = $this->contact_cache[(int) $item['id']];

            $display_name = $contact->get_display_name();


            $title = EZP_IBC_U::__('Edit') . ' ' . $display_name;

            apply_filters("debug", "column display name end");
            return sprintf("<a title='$title' href='$edit_url'>%1\$s</a> %2\$s", $display_name, $this->row_actions($actions));
        }

//
//        function column_event_count($item)
//        {
//            apply_filters("debug", "column event count start");
//
//            $contact_id = (int) $item['id'];
//
//            $events = EZP_IBC_Event_Entity::get_all_by_contact_id($contact_id);
//
//            $view_anchor = sprintf('<a href="?page=%s&tab=events&contact_id=%d">%s</a>', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, $contact_id, number_format(count($events)));
//
//            apply_filters("debug", "column event count end");
//            return $view_anchor;
//        }

        function column_score($item)
        {
            $contact = $this->contact_cache[(int) $item['id']];

            $view_anchor = sprintf('<a href="?page=%s&tab=events&contact_id=%d">%s</a>', EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, $contact->id, number_format($contact->score));

            return $view_anchor;
        }

        function column_notes($item)
        {
            $notes = $item['notes'];

            return $notes;
        }

        function column_list_string($item)
        {
            apply_filters("debug", "column list string start");
            $contact_id = (int) $item['id'];

            //$contact = EZP_IBC_Contact_Entity::get_by_id($contact_id);
            $contact = $this->contact_cache[$contact_id];

            $lists = $contact->get_lists();

            $list_string = '';

            foreach ($lists as $list)
            {
                $list_string .= "$list->name, ";
            }

            if ($list_string != '')
            {
                $list_string = substr($list_string, 0, -2);
            }

            apply_filters("debug", "column list string end");

            return $list_string;
        }
//        function column_stage($item)
//        {
//
//            return EZP_IBC_Contact_Entity::get_stage_string($item['stage']);
//        }
        // </editor-fold>
    }
}