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
    
    $event_range = EZP_IBC_Report_Range_Types::Today;
    
    if(isset($_REQUEST['event_range']))
    {
        switch($_REQUEST['event_range'])
        {
            case 'yesterday':
                $event_range = EZP_IBC_Report_Range_Types::Yesterday;
                break;
            
            case 'this_week':
                $event_range = EZP_IBC_Report_Range_Types::This_Week;
                break;
            
            case 'last_week':
                $event_range = EZP_IBC_Report_Range_Types::Last_Week;
                break;              
            
            case 'last_month':
                $event_range = EZP_IBC_Report_Range_Types::Last_Month;
                break;              
            
            case 'last_7_days':
                $event_range = EZP_IBC_Report_Range_Types::Last_7_Days;
                break;
            
            case 'last_30_days':
                $event_range = EZP_IBC_Report_Range_Types::Last_30_Days;
                break;
        }                
    }
    
    $contacts_url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);
        
    $contacts_url = EZP_IBC_U::append_query_value($contacts_url, 'event_range', $event_range);  
    
    $local_from = EZP_IBC_Report_Helper::get_timestamp_from_report_range($event_range, true);
    $local_to = EZP_IBC_Report_Helper::get_timestamp_from_report_range($event_range, false);
               
    $contacts_table = EZP_IBC_U::get_full_table_name(EZP_IBC_Contact_Entity::$TABLE_NAME);
    $events_table = EZP_IBC_U::get_full_table_name(EZP_IBC_Event_Entity::$TABLE_NAME);
    
    EZP_IBC_U::debug("contacts url3 $contacts_url");
    
?>

<style lang="text/css">
    h2 { font-size: 3em!important; }
    .easy-pie-stats-postbox { margin: 0 50px 50px 0; min-width:0px; height: 340px; width:390px; float:left; }
    .easy-pie-stats-postbox th, .easy-pie-stats-postbox td { padding-bottom: 10px; padding-top: 10px; }
    #easypie-ibc-dashboard h3 { font-size: 2.6em }
    
    .easy-pie-stats-postbox a { font-size: 3em; text-decoration: none}
    #easypie-ibc-dashboard a:hover { text-decoration: underline;}
    #easypie-ibc-dashboard th, #easypie-ibc-dashboard td { line-height: 40px }
    #easypie-ibc-dashboard th { font-size: 1.2em }
    #easypie-ibc-dashboard th { width:265px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display:block; }
    
    #easypie-ibc-overlay {  background-color: rgba(0, 0, 0, 0.2);  z-index: 999;  position: absolute; left: -20px; top: 0; width: 110%; height: 100%; display: none; }
</style>

<div id='easypie-ibc-overlay'></div>
<div class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>

    <div style="height:65px;" >
        <h2 style="float:left;"><?php echo __('Site Activity') . ' - Site Spy'; ?></h2>

        <div style="float:right; margin-right:223px;line-height:50px;">                        
            <form method="get">          
                <h3 style="float:left; font-weight:bold; margin:0 15px 0 0;"><?php EZP_IBC_U::_e('Date Range') ?></h3>
                <input type="hidden" name="page" value="<?php echo EZP_IBC_Constants::$DASHBOARD_SUBMENU_SLUG ?>">
                <select name="event_range" style="margin-bottom:8px; margin-right: 4px;" onchange="jQuery('#easypie-ibc-overlay').show(); this.form.submit();">                   
                    <option value="today" <?php echo $event_range == EZP_IBC_Report_Range_Types::Today ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Today'); ?></option>
                    <option value="yesterday" <?php echo $event_range == EZP_IBC_Report_Range_Types::Yesterday ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Yesterday'); ?></option>
                    <option value="this_week" <?php echo $event_range == EZP_IBC_Report_Range_Types::This_Week ? 'selected' : '' ?>><?php EZP_IBC_U::_e('This Week'); ?></option>
                    <option value="last_week" <?php echo $event_range == EZP_IBC_Report_Range_Types::Last_Week ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Last Week'); ?></option>
                    <option value="last_month" <?php echo $event_range == EZP_IBC_Report_Range_Types::Last_Month ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Last Month'); ?></option>
                    <option value="last_7_days" <?php echo $event_range == EZP_IBC_Report_Range_Types::Last_7_Days ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Last 7 Days'); ?></option>
                    <option value="last_30_days" <?php echo $event_range == EZP_IBC_Report_Range_Types::Last_30_Days ? 'selected' : '' ?>><?php EZP_IBC_U::_e('Last 30 Days'); ?></option>
                </select>
<!--                <button type="submit"><?php EZP_IBC_U::_e('Apply'); ?></button>-->
            </form>
        </div>
    </div>
    
    <div id="easypie-ibc-dashboard" class="inside">        
        <div>
            <div class="postbox easy-pie-stats-postbox" >
                <div class="inside" >
                    <h3><?php EZP_IBC_U::_e("Conversions") ?></h3>
                    <table class="form-table"> 
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
                                <span><a href="<?php echo $url; ?>"><?php echo count(EZP_IBC_Report_Helper::get_filtered_contacts(EZP_IBC_Contact_Stages::Lead, -1, -1)); ?></a></span>
                            </td>
                        </tr>                        
                        <tr>
                            <th scope="row">
                                <?php echo EZP_IBC_U::_e("Customer") ?>
                            </th>
                            <td>
                                <?php
                                    $url = EZP_IBC_U::append_query_value($contacts_url, 'event', EZP_IBC_Contact_Stages::Customer);
                                ?>
                                <span><a href="<?php echo $url; ?>"><?php echo count(EZP_IBC_Report_Helper::get_filtered_contacts(EZP_IBC_Contact_Stages::Customer, -1, -1, -1)); ?></a></span>
                            </td>
                        </tr>                        
                    </table>                    
                </div>
            </div>

            <div class="postbox easy-pie-stats-postbox" >
                <div class="inside" >
                    <h3><?php EZP_IBC_U::_e("Top Events") ?></h3>
                    
                    <?php 
                    $user_event_types = EZP_IBC_User_Event_Type_Entity::get_all();
                    
                    if(count($user_event_types) > 0)
                    {
                        echo '<table class="form-table">';
                                
                        foreach($user_event_types as $user_event_type)
                        {
                            /* @var $user_event_type EZP_IBC_User_Event_Type_Entity */                        
                            $contact_count = count(EZP_IBC_Report_Helper::get_filtered_contacts(-1, EZP_IBC_Event_Types::User_Event, $user_event_type->id, $event_range));

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
                                    <span><a href="<?php echo $url; ?>"><?php echo $contact_count; ?></a></span>
                                </td>                                                  

                            <?php                        
                        }
                        
                        echo '</table>';
                    }
                    else
                    {
                        $href = menu_page_url(EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG, false);
                        $text = __('Define Events');
                        
                        echo "<p ><a style='font-size:1em; text-decoration:underline' href='$href'>$text</a></p>";
                    }
                    
                    ?>                                                                              
                </div>
            </div>           

            <div class="postbox easy-pie-stats-postbox" style='float:left'>
                <div class="inside" >
                    <h3><?php EZP_IBC_U::_e("Top Referrers") ?></h3>
                      <?php 
                        $counts = EZP_IBC_Contact_Entity::get_counts_by_field('referral_path');
                        
                        EZP_IBC_U::debug("contacts url2 $contacts_url");
                        if(count($counts) > 0)
                        {
                            echo '<table class="form-table">';

                            foreach($counts as $count)
                            {
                                ?>                        
                                <tr>
                                    <th title='<?php echo $count->referral_path ?>' scope="row">
                                        <?php echo $count->referral_path; ?>
                                    </th>
                                    <td title='<?php echo $count->count; ?>'>
                                        <?php                                       
                                        $query = "SELECT * FROM $contacts_table WHERE referral_path = '$count->referral_path'";
                                        
                                        $query_info = new stdClass();
                                        $query_info->title = __("Contacts with referral path equal to $count->referral_path");
                                        $query_info->query = $query;
                                        
                                        $query_key = 'ezp-' . EZP_IBC_U::get_guid();
                                                                            
                                        if(set_transient($query_key, $query_info, 60 * 60 * 24) == true)
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
                            }

                            echo '</table>';
                        }
                        else
                        {
                            $text = __('No Contacts.');

                            echo "<h3><$text</h3>";
                        }
                        ?>
                </div>
            </div>

            <div class="postbox easy-pie-stats-postbox" style="float:left;"  >
                <div class="inside" >
                    <h3><?php EZP_IBC_U::_e("Form Submissions") ?></h3>
                    <?php 
                    
                    
                    
                    $local_event_type = EZP_IBC_Event_Types::Form_Submitted;
                    $where_clause = "(type = $local_event_type) AND (timestamp BETWEEN '$local_from' AND '$local_to')";
                    
                    $form_event_counts = EZP_IBC_Event_Entity::get_counts_by_field('parameter', $where_clause);
                    
                                                                                
                    if(count($form_event_counts) > 0)
                    {
                        
                        echo '<table class="form-table">';
                                
                        foreach($form_event_counts as $form_event_count)
                        {   
                            /* @var $user_event_type EZP_IBC_User_Event_Type_Entity */                        
                            $contact_count = count(EZP_IBC_Report_Helper::get_filtered_contacts(-1, EZP_IBC_Event_Types::Form_Submitted, $form_event_count->parameter, $event_range));

                            ?>                        
                            <tr>
                                <th scope="row">
                                    <?php $form_event_count->parameter; ?>
                                </th>
                                <td>
                                    <?php                                        
                                        $form_submit_type = EZP_IBC_Event_Types::Form_Submitted;
                                        $query = "select * from $contacts_table INNER JOIN (SELECT DISTINCT contact_id from $events_table where (type=$form_submit_type) AND (parameter = '$form_event_count->parameter') AND (timestamp BETWEEN '$local_from' AND '$local_to')) as event_contacts on id = event_contacts.contact_id";
                                        $query_info = new stdClass();
                                        $query_info->title = __("Contacts who submitted $form_event_count->parameter between $local_from and $local_to");
                                        $query_info->query = $query;
                                        
                                        $query_key = 'ezp-' . EZP_IBC_U::get_guid();
                                                                            
                                        if(set_transient($query_key, $query_info, 60 * 60 * 24) == true)
                                        {
                                          $url = EZP_IBC_U::append_query_value($contacts_url, 'ezpq', $query_key);                                        
                                        }
                                        else
                                        {
                                            $url = '#';
                                            EZP_IBC_U::debug("Error setting transient query for $query");
                                        }
                                    ?>
                                    <span><a href="<?php echo $url ?>"><?php echo $contact_count; ?></a></span>
                                </td>                                                  

                            <?php                        
                        }
                        
                        echo '</table>';
                    }
                    else
                    {
                        $text = __('No Forms Submitted.');

                        echo "<p>$text</p>";
                    }
                    ?>
                </div>
            </div>

            <div style="clear:both; font-size:1em">            
                <?php EZP_IBC_U::echo_footer_links(); ?>        
            </div>

        </div>

