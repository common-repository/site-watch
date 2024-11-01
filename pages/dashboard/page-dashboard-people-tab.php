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
    .easy-pie-stats-postbox th { padding-top: 11px;   white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width:240px; max-width:240px; font-weight: normal}
    .easy-pie-stats-postbox td { padding-top: 11px; /*padding-bottom: 15px; padding-top: 0px;*/  text-align:center; padding-bottom:20px;}
    .easy-pie-stats-postbox p { margin-left: 10px; }    
    .easy-pie-stats-postbox a { font-size: 1em; text-decoration: none}        
    .easy-pie-stats-postbox i { color: #aaa; font-size:1.1em; margin-top:3px; margin-right: 17px}
    .easy-pie-stats-postbox h3 { margin: 0; }
    .easy-pie-stats-postbox .inside table.form-table { margin-bottom: 4px; margin-top: -6px}
    .easy-pie-stats-header-title { background-color: #eee; margin-bottom:8px; height: 26px; padding:11px 11px 3px 11px;}
    .easy-pie-stats-postbox .inside { margin-top: -6px; }
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
                <h3 style="float:left"><?php EZP_IBC_U::_e("Top Prospects") ?></h3>
            </div>
            <div class="inside" >           
                <?php
                $prospects = EZP_IBC_Contact_Entity::get_all_where('stage = ' . EZP_IBC_Contact_Stages::Prospect, 'score DESC');
                
                $chopped = false;
                
                if(count($prospects) > $Display_Limit)
                {
                    $chopped = true;
                    $prospects = array_slice($prospects, 0, $Display_Limit);
                }
                                
                if (count($prospects) > 0)
                {                    
                    echo '<table class="form-table">';
                    echo '<th style="font-weight:bold;padding-bottom:0">' . __('Name') . '</th>' . '<td style="font-weight:bold;padding-bottom:0">' . __('Score') . '</td>';
                    foreach ($prospects as $prospect)
                    {  
                        $prospect_url = menu_page_url(EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, false);
                        $prospect_url = EZP_IBC_U::append_query_value($prospect_url, 'contact_id', $prospect->id);
                        
                        $prospect_edit_url = EZP_IBC_U::append_query_value($prospect_url, 'tab', 'edit');
                        $prospect_event_url = EZP_IBC_U::append_query_value($prospect_url, 'tab', 'events');
                        
                        
                        ?>
                        <tr>
                            <th style="font-weight:normal" scope="row">
                                <a href="<?php echo $prospect_edit_url; ?>"><?php echo $prospect->get_display_name(); ?></a>
                            </th>
                            <td>                                
                                <a href="<?php echo $prospect_event_url; ?>"><?php echo $prospect->score ?></a>
                            </td>                                                  
                        </tr>
                        <?php
                    }

                    echo '</table>';
                    
                    if ($chopped)
                    {                        
                        $url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);
                        
                        $url = EZP_IBC_U::append_query_value($url, 'stage', EZP_IBC_Contact_Stages::Prospect);
                        $url = EZP_IBC_U::append_query_value($url, 'orderby', 'score');
                        $url = EZP_IBC_U::append_query_value($url, 'order', 'desc');

                        echo __("<a style='font-size:1em; text-decoration:underline' href='$url'>All Prospects</a>");
                    }
                }
                else
                {
                    $text = __('No Prospects');

                    echo "<p >$text</p>";
                }
                    ?>                                                                              
            </div>
        </div>    
        
        <div class="postbox easy-pie-stats-postbox" >
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php EZP_IBC_U::_e("Top Leads") ?></h3>
            </div>
            <div class="inside" >           
                <?php
                $leads = EZP_IBC_Contact_Entity::get_all_where('stage = ' . EZP_IBC_Contact_Stages::Lead, 'score DESC');
                
                $chopped = false;
                
                if(count($leads) > $Display_Limit)
                {
                    $chopped = true;
                    $leads = array_slice($leads, 0, $Display_Limit);
                }
                                
                if (count($leads) > 0)
                {                    
                    echo '<table class="form-table">';
                    echo '<th style="font-weight:bold;padding-bottom:0">' . __('Name') . '</th>' . '<td style="font-weight:bold;padding-bottom:0">' . __('Score') . '</td>';
                    foreach ($leads as $lead)
                    {  
                        $lead_url = menu_page_url(EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, false);
                        $lead_url = EZP_IBC_U::append_query_value($lead_url, 'contact_id', $lead->id);
                        
                        $lead_edit_url = EZP_IBC_U::append_query_value($lead_url, 'tab', 'edit');
                        $lead_event_url = EZP_IBC_U::append_query_value($lead_url, 'tab', 'events');
                        
                        ?>
                        <tr>
                            <th style="font-weight:normal" scope="row">
                                <a href="<?php echo $lead_edit_url; ?>"><?php echo $lead->get_display_name(); ?></a>
                            </th>
                            <td>
                                <a href="<?php echo $lead_event_url; ?>"><?php echo $lead->score; ?></a>
                            </td>                                                  
                        </tr>
                        <?php
                    }

                    echo '</table>';
                    
                    if ($chopped)
                    {
                        $url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);
                        
                        $url = EZP_IBC_U::append_query_value($url, 'stage', EZP_IBC_Contact_Stages::Lead);
                        $url = EZP_IBC_U::append_query_value($url, 'orderby', 'score');
                        $url = EZP_IBC_U::append_query_value($url, 'order', 'desc');

                        echo __("<a style='font-size:1em; text-decoration:underline' href='$url'>All Leads</a>");
                    }
                }
                else
                {
                    $text = __('No Leads');

                    echo "<p >$text</p>";
                }
                    ?>                                                                              
            </div>
        </div>   
        
        <div class="postbox easy-pie-stats-postbox" style="clear:both;" >
            <div class="easy-pie-stats-header-title" >
                <h3 style="float:left"><?php EZP_IBC_U::_e("Top Customers") ?></h3>
            </div>
            <div class="inside" >           
                <?php
                $customers = EZP_IBC_Contact_Entity::get_all_where('stage = ' . EZP_IBC_Contact_Stages::Customer, 'score DESC');
                
                $chopped = false;
                
                if(count($customers) > $Display_Limit)
                {
                    $chopped = true;
                    $customers = array_slice($customers, 0, $Display_Limit);
                }
                                
                if (count($customers) > 0)
                {                    
                    echo '<table class="form-table">';
                    echo '<th style="font-weight:bold;padding-bottom:0">' . __('Name') . '</th>' . '<td style="font-weight:bold;padding-bottom:0">' . __('Score') . '</td>';
                    foreach ($customers as $customer)
                    {  
                        $customer_url = menu_page_url(EZP_IBC_Constants::$CONTACT_SUBMENU_SLUG, false);
                        $customer_url = EZP_IBC_U::append_query_value($customer_url, 'contact_id', $customer->id);
                        
                        $customer_edit_url = EZP_IBC_U::append_query_value($customer_url, 'tab', 'edit');
                        $customer_event_url = EZP_IBC_U::append_query_value($customer_url, 'tab', 'events');
                        
                        ?>
                        <tr>
                            <th style="font-weight:normal" scope="row">
                                <a href="<?php echo $customer_edit_url; ?>"><?php echo $customer->get_display_name(); ?></a>
                            </th>
                            <td>
                                <a href="<?php echo $customer_event_url; ?>"><?php echo $customer->score; ?></a>
                            </td>                                                  
                        </tr>
                        <?php
                    }

                    echo '</table>';
                    
                    if ($chopped)
                    {
                        $url = menu_page_url(EZP_IBC_Constants::$PEOPLE_SUBMENU_SLUG, false);
                        
                        $url = EZP_IBC_U::append_query_value($url, 'stage', EZP_IBC_Contact_Stages::Customer);
                        $url = EZP_IBC_U::append_query_value($url, 'orderby', 'score');
                        $url = EZP_IBC_U::append_query_value($url, 'order', 'desc');

                        echo __("<a style='font-size:1em; text-decoration:underline' href='$url'>All Customers</a>");
                    }
                }
                else
                {
                    $text = __('No Customers');

                    echo "<p >$text</p>";
                }
                    ?>                                                                              
            </div>
        </div>   
    </div>
</div>