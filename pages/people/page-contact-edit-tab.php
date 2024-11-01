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
/* @var $contact EZP_IBC_Contact_Entity */

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-list-entity.php');

$url_type = EZP_IBC_Event_Types::Page_Load;
$url_count = EZP_IBC_Event_Entity::get_count_where("(contact_id = $contact->id) and (type = $url_type)");

$user_event_type = EZP_IBC_Event_Types::User_Event;
$user_event_count = EZP_IBC_Event_Entity::get_count_where("(contact_id = $contact->id) and (type = $user_event_type)");

//$full_events = EZP_IBC_Event_Entity::get_all_where("contact_id = $contact->id", 'timestamp desc');

//$total_event_count = count($full_events);

?>
<style type="text/css">
    
    .easy-pie-stats-table td div { width: 300px; overflow: hidden; text-overflow: ellipsis; max-width:300px;}
    .easy-pie-stats-table i { color: #aaa; font-size:1.1em; margin-left:7px}
</style>

<input type="hidden" name="form_action" value="save"/>
<input type="hidden" name="contact_id" value="<?php echo $contact->id; ?>"/> <!-- Required for postback update - initially sent from get -->
<?php
if ($error_string != "") :
    ?>
    <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
<?php endif; ?>

<?php 

if ($action_updated){ ?>
    <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Contact Updated.'); ?></div>
<?php }

if ($action_rescored){ ?>
    <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Score Recalculated.'); ?></div>
<?php }

?>

<div>
    
    <div style="float:left; width:75%">
        <div class="postbox" >            
            <div class="inside" >
                <h3 style="margin-bottom:0px;"><?php _e("Behavior") ?></h3>
                <table style="width:875px;">
                    <tr>
                        <td style="vertical-align:top">
                            <table class="form-table easy-pie-stats-table"> 
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("Lifecycle") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo EZP_IBC_Contact_Entity::get_stage_string($contact->stage); ?></div>
                                        </div>
                                    </td>
                                </tr>                                                                                        
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("First Contact") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo EZP_IBC_U::get_wp_formatted_from_gmt_formatted_time($contact->creation_timestamp); ?></div>
                                        </div>
                                    </td>
                                </tr>                                
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("Last Activity") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo $contact->last_activity_timestamp == null ? __('Unknown') : EZP_IBC_U::get_wp_formatted_from_gmt_formatted_time($contact->last_activity_timestamp); ?></div>
                                        </div>
                                    </td>
                                </tr>                                    
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("Referral Path") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div title="<?php echo $contact->referral_path; ?>"><?php echo $contact->referral_path === '' ? __('Direct') : $contact->referral_path; ?></div>                                            
                                        </div>
                                    </td>
                                </tr>    
                                <tr>
                                    <th style="padding-bottom:0px;" scope="row">
                                        <button style="font-weight:normal" type="submit" onclick="jQuery('[name=form_action]').val('re_score'); return true;">Re-Score</button><i title='<?php _e('Uses current event worths. Purged events disregarded'); ?>' class='fa fa-question-circle'></i>
                                    </th>
                                    <td>
                                      
                                    </td>
                                </tr>    
                            </table>
                        </td>

                        <td style="vertical-align:top">
                            <table class="form-table easy-pie-stats-table"> 
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("Score") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo number_format($contact->score); ?></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("URLs Visited") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo number_format($url_count); ?></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("User Events") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div><?php echo number_format($user_event_count); ?></div>
                                        </div>
                                    </td>
                                </tr>                                
                                <tr>
                                    <th scope="row">
                                        <?php echo EZP_IBC_U::_e("Last Host") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <div>
                                            <?php 
                                            $last_hostname = $contact->get_last_hostname();
                                            echo "<a target='_blank' href='http://www.ip-adress.com/ip_tracer/$last_hostname'>$last_hostname</a>" 
                                            ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>    
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="easy-pie-outer-div" class="postbox"  style="margin-top:12px;">
            <div class="inside" >
                <h3><?php EZP_IBC_U::_e("Basic Information") ?></h3>
                <table class="form-table">                                                            
                    <tr <?php
                    if ($contact->wpid == -1)
                    {
                        echo 'style="display:none;" ';
                    }
                    ?>>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Login") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input readonly type="text" value="<?php
                                if ($contact->wpid != -1)
                                {
                                    echo $contact->_wp_login;
                                }
                                ?>" />                        
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Email") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input name="email" type="text" value="<?php echo $contact->email; ?>" />                        
                            </div>
                        </td>
                    </tr>  
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("Last Name") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input name="last_name" type="text" value="<?php echo $contact->last_name; ?>" />
                            </div>
                        </td>
                    </tr>  
                    <tr>
                        <th scope="row">
                            <?php echo EZP_IBC_U::_e("First Name") ?>
                        </th>
                        <td>
                            <div class="compound-setting">                            
                                <input name="first_name" type="text" value="<?php echo $contact->first_name; ?>" />
                            </div>
                        </td>
                    </tr>                    
                    <tr>
                    <th scope="row">
                            <?php echo EZP_IBC_U::_e("Notes") ?>
                        </th>
                        <td style="padding-bottom:0">
                            <div class="compound-setting">                            
                                <textarea name="notes" rows="3" cols="50"><?php echo $contact->notes; ?></textarea>
                            </div>
                        </td>
                    </tr>    
                </table>
            </div>
        </div>
    </div>
    <div class="postbox" style="float:right;border: black solid 1px; width: 20%; min-width:0px; height: 400px; margin-top:12px; margin-right:10px;">        
        <div class="handlediv" title="Click to toggle"><br></div>
        <h3 style="margin-left:5px" class="hndle">
            <span><?php echo EZP_IBC_U::__('List Membership'); ?></span>
        </h3>
        <?php
        $lists = EZP_IBC_List_Entity::get_all();
        $contact_memberships = $contact->get_list_ids();

        foreach ($lists as $list)
        {
            /* @var $list EZP_IBC_List_Entity */
            $is_member = in_array($list->id, $contact_memberships);
            $checked = '';

            if ($is_member)
            {
                $checked = 'checked';
            }

            echo "<div style='margin-top:15px; margin-left:20px'><input type='checkbox' name='list_ids[]' value='$list->id' $checked>$list->name</option></div>";
        }
        ?>
        </select>
    </div>
</div>
<p style="clear:both"> 
    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Update'); ?>" />   
</p>                