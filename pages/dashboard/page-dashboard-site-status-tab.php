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
?>

<?php
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-ecommerce.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-report-helper.php');

$contacts_url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);

$event_range = -1;

$local_from = '1900-01-01';
$local_to = '2100-01-01';

$contacts_table = EZP_IBC_U::get_full_table_name(EZP_IBC_Contact_Entity::$TABLE_NAME);
$events_table = EZP_IBC_U::get_full_table_name(EZP_IBC_Event_Entity::$TABLE_NAME);

$Display_Limit = 4;
?>

<style lang="text/css">
    .easy-pie-stats-postbox { margin: 0 70px 50px 0; min-width:0px; height: 300px; width:320px; float:left; border: solid black 1px; 
                              -webkit-box-shadow: 0 10px 6px -6px #777; -moz-box-shadow: 0 10px 6px -6px #777; box-shadow: 0 10px 6px -6px #777;}
    .easy-pie-stats-postbox th { padding-top: 11px;   white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width:240px; max-width:240px;}
    .easy-pie-stats-postbox td { padding-bottom: 15px; padding-top: 0px;  text-align:center}
    .easy-pie-stats-postbox p { margin-left: 10px; }    
    .easy-pie-stats-postbox a { font-size: 1.6em; text-decoration: none}        
    .easy-pie-stats-postbox i { color: #aaa; font-size:1.1em; margin-top:3px; margin-right: 17px}
    .easy-pie-stats-postbox h3 { margin: 0; }
    .easy-pie-stats-header-title { background-color: #eee; margin-bottom:8px; height: 26px; padding:11px 11px 3px 11px;}
</style>

<div style="margin-top:44px;">

    <div>
        <?php
        echo EZP_IBC_Storage_Manager::get_alert_text();
        ?>        
    </div>

    <div>
        <div class="postbox easy-pie-stats-postbox" >
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php EZP_IBC_U::_e("Lifecycle") ?></h3><div style="float:right"><i title='<?php _e('Contacts by lifecycle'); ?>' class='fa fa-question-circle'></i></div>
            </div>
            <div class="inside" >
                
                <table class="form-table"> 
<!--                        <tr>
                        <th style="display:table-cell">
                            Lifecycle
                        </th>
                        <th style="display:table-cell">
                            Contacts
                        </th>
                    </tr>-->
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Prospect") ?>
                        </th>
                        <td>
                            <?php
                            $url = EZP_IBC_U::append_query_value($contacts_url, 'stage', EZP_IBC_Contact_Stages::Prospect);
                            ?>
                            <span><a href="<?php echo $url; ?>"><?php echo count(EZP_IBC_Report_Helper::get_filtered_contacts(EZP_IBC_Contact_Stages::Prospect, -1, -1, $event_range)); ?></a></span>
                        </td>
                    </tr>                        
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Lead") ?>
                        </th>
                        <td>
                            <?php
                            $url = EZP_IBC_U::append_query_value($contacts_url, 'stage', EZP_IBC_Contact_Stages::Lead);
                            ?>
                            <span><a href="<?php echo $url; ?>"><?php echo count(EZP_IBC_Report_Helper::get_filtered_contacts(EZP_IBC_Contact_Stages::Lead, -1, -1, $event_range)); ?></a></span>
                        </td>
                    </tr>                        
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Customer") ?>
                        </th>
                        <td>
                            <?php
                            
                            $customer_count = count(EZP_IBC_Report_Helper::get_filtered_contacts(EZP_IBC_Contact_Stages::Customer, -1, -1, $event_range));
                            
                            if($global->ecommerce_mode == EZP_IBC_ECommerce_Modes::NONE)
                            {
                                if($customer_count == 0)
                                {
                                    $inner_html = __('<span style="font-size:1.6em; cursor:help" title="No eCommerce system configured">N/A</span>');
                                }
                                else
                                {
                                    $url = EZP_IBC_U::append_query_value($contacts_url, 'stage', EZP_IBC_Contact_Stages::Customer);
                                    $title = __('eCommerce system presently disabled');
                                    $inner_html = "<a title='$title' href='$url'>$customer_count</a>";
                                }
                            }
                            else
                            {                                
                                $url = EZP_IBC_U::append_query_value($contacts_url, 'stage', EZP_IBC_Contact_Stages::Customer);
                                $inner_html = "<a href='$url'>$customer_count</a>";
                            }
                            
                            ?>
                            
                            <span><?php echo $inner_html; ?></span>
                        </td>
                    </tr>                        
                </table>                    
            </div>
        </div>

        <div class="postbox easy-pie-stats-postbox" >
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php _e("User Events") ?></h3><div style="float:right"><i title='<?php _e('Contacts who tripped the top user events'); ?>' class='fa fa-question-circle'></i></div>
            </div>
            
            <div class="inside" >
                <?php
                $user_event_types = EZP_IBC_User_Event_Type_Entity::get_all();

                $num_user_event_types = count($user_event_types);
                if ($num_user_event_types > 0)
                {
                    $user_event_type_counts = array();

                    foreach ($user_event_types as $user_event_type)
                    {
                        /* @var $user_event_type EZP_IBC_User_Event_Type_Entity */
                        $contact_count = count(EZP_IBC_Report_Helper::get_filtered_contacts(-1, EZP_IBC_Event_Types::User_Event, $user_event_type->id, $event_range));

                        $user_event_type_counts[$user_event_type->id] = $contact_count;
                    }

                    arsort($user_event_type_counts);

                    $chopped = $num_user_event_types > $Display_Limit;

                    $c = 0;

                    echo '<table class="form-table">';
                    foreach ($user_event_type_counts as $user_event_type_id => $user_event_type_count)
                    {
                        
                            
                        $user_event_type = EZP_IBC_User_Event_Type_Entity::get_by_id($user_event_type_id);

                        ?>
                        <tr>
                            <th scope="row">
                                <?php _e($user_event_type->name); ?>
                            </th>
                            <td>
                                <?php
                                $encoded_event = EZP_IBC_Event_Types::User_Event . "P$user_event_type->id";

                                $url = EZP_IBC_U::append_query_value($contacts_url, 'event', $encoded_event);
                                ?>
                                <span><a href="<?php echo $url; ?>"><?php echo $user_event_type_count; ?></a></span>
                            </td>                                                  

                            <?php
                            $c++;
                            
                            if ($c == $Display_Limit)
                            {
                                break;
                            }
                        }

                        echo '</table>';

                        if (count($user_event_type_counts) > $Display_Limit)
                        {
                            echo '<p>*' . __('Only top event types shown') . '</p>';
                        }
                    }
                    else
                    {
                        $href = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);

                        $href = EZP_IBC_U::append_query_value($href, 'tab', 'types');

                        $text = __('No User Events Defined');

                        echo "<p ><a style='font-size:1em; text-decoration:underline' href='$href'>$text</a></p>";
                    }
                    ?>                                                                              
            </div>
        </div>           

        <div class="postbox easy-pie-stats-postbox" style='clear:both'>
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php _e("Source") ?></h3><div style="float:right"><i title='<?php _e('Contacts by initial referral source'); ?>' class='fa fa-question-circle'></i></div>
            </div>
            
            <div class="inside" >                
                <?php
                $counts = EZP_IBC_Contact_Entity::get_counts_by_field('referral_path');

                if (count($counts) > 0)
                {
                    $chopped = $num_user_event_types > $Display_Limit;

                    $c = 0;

                    echo '<table class="form-table">';

                    foreach ($counts as $count)
                    {
                        $referral_display = $count->referral_path;
                        
                        if(empty($referral_display))
                        {                        
                            $referral_display = __('Direct');
                        }
                        
                        ?>                        
                        <tr>
                            <th style="cursor:help" title='<?php echo $referral_display ?>' scope="row">
                                <?php echo $referral_display; ?>
                            </th>                            
                            <td title='<?php echo $count->count; ?>'>
                                <?php

                                $query = "SELECT * FROM $contacts_table WHERE referral_path = '$count->referral_path'";

                                $query_info = new stdClass();
                                $query_info->title = __("Contacts with referral path equal to $referral_display");
                                $query_info->query = $query;

                                $query_key = 'ezp-' . EZP_IBC_U::get_guid();

                                if (set_transient($query_key, $query_info, 60 * 60 * 24) == true)
                                {
                                    $url = EZP_IBC_U::append_query_value($contacts_url, 'ezpq', $query_key);
                                }
                                else
                                {
                                    $url = '#';
                                    EZP_IBC_U::debug("Error setting transient query for $query");
                                }
                                ?>
                                <span><a href="<?php echo $url ?>"><?php echo $count->count; ?></a></span>
                            </td>                                                  

                            <?php
                            $c++;
                            if ($c == $Display_Limit)
                            {
                                break;
                            }
                        }

                        echo '</table>';

                        if (count($counts) > $Display_Limit)
                        {
                            echo '<p>*' . __('Only top sources shown') . '</p>';
                        }
                    }
                    else
                    {
                        $text = __('No Contacts');

                        echo "<p>$text</p>";
                    }
                    ?>
            </div>
        </div>

        <div class="postbox easy-pie-stats-postbox" style="float:left;"  >
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php _e("Forms") ?></h3><div style="float:right"><i title='<?php _e('Contacts who submitted top forms'); ?>' class='fa fa-question-circle'></i></div>
            </div>
            <div class="inside" >
                <?php
                $local_event_type = EZP_IBC_Event_Types::Form_Submitted;
                $where_clause = "(type = $local_event_type) AND (timestamp BETWEEN '$local_from' AND '$local_to')";

                $parameters = array('parameter', 'parameter2', 'parameter3');

                $form_event_counts = EZP_IBC_Event_Entity::get_counts_by_fields($parameters, $where_clause);

                if (count($form_event_counts) > 0)
                {
                    $chopped = $num_user_event_types > $Display_Limit;

                    $c = 0;

                    echo '<table class="form-table">';

                    foreach ($form_event_counts as $form_event_count)
                    {
                        /* @var $user_event_type EZP_IBC_User_Event_Type_Entity */
                        // $contact_count = count(EZP_IBC_Report_Helper::get_filtered_contacts(-1, EZP_IBC_Event_Types::Form_Submitted, $form_event_count->parameter, $event_range));
//                            $form_submitted_virtual_name = EZP_IBC_Event_Entity::get_form_submitted_event_virtual_name($form_event_count->parameter);
                        // parameter = form id, parameter2=url
                        ?>                        
                        <tr>
                            <th style="cursor:help" scope="row" title='<?php echo "Submitted from $form_event_count->parameter2"; ?>'>
                                <?php
                                _e($form_event_count->parameter);
                                ?>
                            </th>

                            <?php
                            $form_submit_type = EZP_IBC_Event_Types::Form_Submitted;
                            $query = "select * from $contacts_table INNER JOIN (SELECT DISTINCT contact_id from $events_table where (type=$form_submit_type) AND (parameter = '$form_event_count->parameter') AND (parameter2 = '$form_event_count->parameter2') AND (timestamp BETWEEN '$local_from' AND '$local_to')) as event_contacts on id = event_contacts.contact_id";

                            $query_info = new stdClass();
                            $query_info->title = __("Contacts who submitted form $form_event_count->parameter from $form_event_count->parameter2");
                            $query_info->query = $query;

                            $query_key = 'ezp-' . EZP_IBC_U::get_guid();

                            if (set_transient($query_key, $query_info, 60 * 60 * 24) == true)
                            {
                                $url = EZP_IBC_U::append_query_value($contacts_url, 'ezpq', $query_key);
                            }
                            else
                            {
                                $url = '#';
                                EZP_IBC_U::debug("Error setting transient query for $query");
                            }
                            ?>
                            <td>
                                <span><a href="<?php echo $url ?>"><?php echo $form_event_count->count; ?></a></span>
                            </td>                                                  

                        </tr>
                        <?php
                        $c++;
                        if ($c == $Display_Limit)
                        {
                            break;
                        }
                    }

                    echo '</table>';

                    if (count($form_event_counts) > $Display_Limit)
                    {
                        // TODO put this in help
                        $href = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);
                        $text = __('Contacts Page');

                        $anchor_text = "<a style='font-size:1em; text-decoration:underline' href='$href'>$text</a></p>";

                        echo __('For more form activity, filter') . ' ' . $anchor_text . '.';
                    }
                }
                else
                {
                    $text = __('No Forms Submitted');

                    echo "<p>$text</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>