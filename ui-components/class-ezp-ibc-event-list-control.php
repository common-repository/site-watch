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

if (!class_exists('EZP_IBC_Event_List_Control'))
{

    /**
     * @author Bob Riley <bob@easypiewp.com>
     * @copyright 2015 Synthetic Thought LLC
     */
    class EZP_IBC_Event_List_Control extends WP_List_Table
    {
        private $contact_id;
        private $event;
        private $search;
        private $nonce_action;
        
        private $current_session_number = -1;

        public function __construct($contact_id, $event, $search, $nonce_action)
        {
            parent::__construct();

            $this->contact_id = $contact_id;
            $this->event = $event;
            $this->search = $search;
            $this->nonce_action = $nonce_action;
        }

        public function extra_tablenav($which)
        {
            if ($which == 'top')
            {
                ?>
                <form>
                    <div class='alignleft'>                                    
                        <input type="hidden" name="page" value="<?php echo EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG ?>" />
                        <input type="hidden" name="tab" value="events" />
                        <input type="hidden" name="contact_id" value="<?php echo $this->contact_id ?>" />
                        <div id="ezp_filters">

                            <select class='actions' name="event" id="ezp_ibc_stage_filter">
                                <option value="-1" <?php echo $this->event == -1 ? 'selected' : '' ?>><?php _e('All Events'); ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::Page_Load ?>" <?php echo $this->event == EZP_IBC_Event_Types::Page_Load ? 'selected' : '' ?>><?php _e('Page Loads') ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::Form_Submitted ?>" <?php echo $this->event == EZP_IBC_Event_Types::Form_Submitted ? 'selected' : '' ?>><?php _e('Form Submissions') ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::User_Event ?>" <?php echo $this->event == EZP_IBC_Event_Types::User_Event ? 'selected' : '' ?>><?php _e('User Events') ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::WP_Login ?>" <?php echo $this->event == EZP_IBC_Event_Types::WP_Login ? 'selected' : '' ?>><?php _e('Logins') ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::Stage_Change ?>" <?php echo $this->event == EZP_IBC_Event_Types::Stage_Change ? 'selected' : '' ?>><?php _e('Lifecycle Changes') ?></option>
                                <option value="<?php echo EZP_IBC_Event_Types::Purchase ?>" <?php echo $this->event == EZP_IBC_Event_Types::Purchase ? 'selected' : '' ?>><?php _e('Purchases') ?></option>                               
                            </select>

                            <button type="submit" class="button action"><?php _e('Filter'); ?></button>
                        </div>
                    </div>
                </form>
                <?php
            }
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
                case 'timestamp':
                case 'type':
                case 'session_number':
                //   case 'parameter':
                case 'virtual_name':
                case 'worth':
                    //  case 'group':
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
            $columns = array(
                'id' => __('ID'),
                'type' => __('Type'),
                'virtual_name' => __('Detail'),
                'timestamp' => __('Timestamp'),
                'worth' => __('Worth')
                //'session_number' => __('Session')
            );

            return $columns;
        }

        public function get_hidden_columns()
        {
            return array('id');
        }

        public function get_sortable_columns()
        {
            return array('timestamp' => array('timestamp', true), 'worth' => array('worth', 'false'));
        }

        private function get_table_data()
        {
            if ($this->event == -1)
            {
                $events = EZP_IBC_Event_Entity::get_all_by_contact_id($this->contact_id, "id asc");
            }
            else
            {
                $events = EZP_IBC_Event_Entity::get_all_where("(contact_id = $this->contact_id) AND (type = $this->event)", "id asc");
            }

            $data = array();

            $previous_ticks = 0;
            $session_count = 0;
            $session_tolerance = 60 * 30;

            foreach ($events as $event)
            {
                /* @var $event EZP_IBC_Event_Entity */

                $event->virtual_name = '';

                $current_ticks = strtotime($event->timestamp);
                if (($current_ticks - $previous_ticks) > $session_tolerance)
                {
                    $session_count++;
                }

                $event->session_number = $session_count;

                $previous_ticks = $current_ticks;

                $event->worth = EZP_IBC_Event_Entity::get_worth($event->type, $event->parameter, $event->parameter2, $event->parameter3);
                
                $data[] = (array) $event;
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
               
        function column_timestamp($item)
        {
            return EZP_IBC_U::get_simplified_local_time_from_formatted_gmt($item['timestamp']);
        }

        function column_virtual_name($item)
        {
            $event_id = $item['id'];

            $event = EZP_IBC_Event_Entity::get_by_id($event_id);
            $virtual_name = $event->get_virtual_name();

            $details = $event->get_display_details();

            $text = '';

            if (!empty($details))
            {
                //if(($event->type == EZP_IBC_Event_Types::Form_Submitted) || ($event->type == EZP_IBC_Event_Types::Purchase) || $event->type == EZP_IBC_Event_Types::User_Event)
                //    $text = '<div>';

                $text .= "<div title='$event->parameter' style='cursor:pointer' onclick='jQuery(this).children(\"div\").toggle();'>" . '<i style="margin-right: 4px; font-size:1.15em" class="fa fa-caret-right"></i>' . $virtual_name;
                $text .= '<div style="display:none; margin-top:5px">' . $details . '</div>';
                $text .= '</div>';
//                $text .= '</div>';
            }
            else
            {
                if ($event->type == EZP_IBC_Event_Types::Page_Load)
                {
                    $virtual_name = "<a href='$event->parameter' target='_blank'>$virtual_name</a>";
                }

                $text = $virtual_name;
            }

            return $text;
        }

        // <editor-fold desc="Column display functions">

        function column_type($item)
        {
            $event_type_string = EZP_IBC_U::__("Unknown");


            switch ($item['type'])
            {

                case EZP_IBC_Event_Types::Page_Load:
                    $event_type_string = __('Page Load');
                    break;

                case EZP_IBC_Event_Types::Form_Submitted:
                    $event_type_string = __('Form Submission');
                    break;

                case EZP_IBC_Event_Types::Button_Clicked:
                    $event_type_string = __('Button Click');
                    break;

                case EZP_IBC_Event_Types::Stage_Change:
                    $event_type_string = __('Lifecycle Change');
                    break;

                case EZP_IBC_Event_Types::Purchase:
                    $event_type_string = __('Purchase');
                    break;

                case EZP_IBC_Event_Types::WP_Login:
                    $event_type_string = __('WordPress Login');
                    break;

                case EZP_IBC_Event_Types::User_Event:
                    $event_type_string = __('User Event');
                    break;
            }

            $event_id = $item['id'];

            $page_url = menu_page_url(EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, false);

            $page_url = EZP_IBC_U::append_query_value($page_url, 'tab', 'events');
            $page_url = EZP_IBC_U::append_query_value($page_url, 'contact_id', $this->contact_id);
            $page_url = EZP_IBC_U::append_query_value($page_url, 'event_id', $event_id);
            $page_url = EZP_IBC_U::append_query_value($page_url, 'action', 'delete');

            $page_url = wp_nonce_url($page_url, $this->nonce_action);

            $delete_url = "<a href='$page_url'>Delete</a>";

            $actions = array(
                'delete' => $delete_url,
            );

            $event_session_number = $item['session_number'];
            
            
            if($this->current_session_number != $event_session_number)
            {
                $this->current_session_number = $event_session_number;
                
                $session_text = sprintf(__('Start of session %1$s'), $event_session_number);
                
                return sprintf('%1$s <span style="cursor:help" title="%3$s">*</span> %2$s', $event_type_string, $this->row_actions($actions), $session_text);
            }
            else
            {
                return sprintf('%1$s %2$s', $event_type_string, $this->row_actions($actions));
            }
        }
        // </editor-fold>
    }
}