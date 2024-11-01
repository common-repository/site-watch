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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/class-ezp-ibc-contact-list-control.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-list-entity.php');

// Determine if the list control threw any actions on postback
if (isset($_REQUEST['form-action'])) {      
    $action = $_REQUEST['form-action'];

    if ($action == 'delete') {
        if (isset($_REQUEST['contact_id'])) {
            $contact_id = $_REQUEST['contact_id'];

            EZP_IBC_Contact_Entity::delete_by_id($contact_id);
        }
    }
}


?>
<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/people/page-people-contacts-tab.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<script type="text/javascript">
 //   easyPie.IBC.Contacts.PageContacts.confirmDelete = function(yesCallback) {
    
//    $("#easy-pie-delete-confirm").dialog({
//        modal: true,
//        buttons: 
//                { "<?php _e('Delete') ?>": function { yesCallback(); $(this).dialog("close") } },
//                { "<?php _e('Cancel') ?>": function { $(this).dialog("close") } }
//        
//    });
</script>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">
<?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>    
    <?php
    
    $event = -1;
    $event_range = -1;
    $event_parameter = -1;
    $stage = -1;
    $search = null;
    $query_info = null;
      
    if(isset($_REQUEST['ezpq']))
    {
        $query_key = $_REQUEST['ezpq'];
        $query_info = get_transient($query_key);
        
        EZP_IBC_U::debug("query key = $query_key");
        
        if($query_info == false)
        {
            $query_info = false;
            EZP_IBC_U::debug("Error retrieving query info with key $query_key");
        }
        else
        {
         //   EZP_IBC_U::debug($query_info);
        }
        
        $query_info_check = strtolower($query_info->query);
        
        EZP_IBC_U::debug("query_info_check $query_info_check");
        
        if((EZP_IBC_U::starts_with($query_info_check, 'select') == false))
        {
            $query_info = false;
            EZP_IBC_U::debug("There is soemething wrong this the transient query: $query_info");
        }                        
    }
    
    if (isset($_REQUEST['s']))
    {
        $search = trim($_REQUEST['s']);
        
        if($search == '')
        {
            $search = null;
        }
    } 

    if (isset($_REQUEST['event']))
    {
        $event = $_REQUEST['event'];                
                
        if(strpos($event,'P') != false)
        {
            $event_array = explode('P', $event);
            
            $event = $event_array[0];
            $event_parameter = $event_array[1];
        }      
        
        if(isset($_REQUEST['event_range']))
        {
            $event_range = $_REQUEST['event_range'];
        }
    }
    
    //EZP_IBC_U::debug($_REQUEST);
    
    if (isset($_REQUEST['stage']))
    {
        $stage = $_REQUEST['stage'];
    }
    
    $contact_list_control = new EZP_IBC_Contact_List_Control($stage, $event, $event_parameter, $event_range, $search, $query_info, $nonce_action);
    $contact_list_control->prepare_items();
         
    if($query_info == null)
    {
        $stage_text = $stage == -1 ? __('All Contacts') : EZP_IBC_Report_Helper::get_stage_text($stage);
        $event_text = $event == -1 ? '' : strtolower(EZP_IBC_Report_Helper::get_event_text($event, $event_parameter));
        $event_range_text = $event_range == -1 ? '' : EZP_IBC_Report_Helper::get_range_text($event_range);

        if($event != -1)
        {
            $with = __('with');
            $list_subtype_text = "$stage_text $with $event_text $event_range_text";
        }
        else
        {
            $list_subtype_text = "$stage_text";
        }
    }
    else
    {
        $list_subtype_text = $query_info->title;
    }
        
    $export_parameters = "$stage, $event, $event_parameter, $event_range";
    //echo '<h3>' . __('Filter:') . $list_subtype_text . '</h3>';
    
    ?>
    <div class="wrap">

        <ul class="subsubsub">
            <li class="all"><a href="#" class="current"><?php echo "$list_subtype_text " ?></a></li>
        </ul>
     
            
            <?php
                $contact_list_control->search_box('search', 'contact_listsearch_id'); 
            ?>

            
            <div id="easypie-cs-options" class="inside">
            <?php $contact_list_control->display(); ?>            
            </div>
      
        
        <div style="margin: 35px 0 25px 0;">            
            <button class="button" onclick="easyPie.IBC.People.PagePeople.ContactsTab.exportContacts(<?php echo $export_parameters; ?>);return false;"><?php echo __('Export All'); ?></button>
            <?php 
            $lists = EZP_IBC_List_Entity::get_all();
            
            if(count($lists) > 0)
            {              
                echo "<select style='margin-left:25px' id='ezp-ibc-list-select'>";

                $lists = EZP_IBC_List_Entity::get_all();
                    
                foreach($lists as $list)
                {
                    echo "<option value='$list->id'>$list->name</option>";
                }
                    
               echo '</select>';
               
               $anchor_text = __('Add All to List');
            
                echo "<button style='margin-left:5px' class='button' onclick='easyPie.IBC.People.PagePeople.ContactsTab.addContactsToList($export_parameters)'>$anchor_text</button>";
            }                       
            ?>
            
        </div>
    </div>
</div>
<div style="display:none" id="easy-pie-delete-confirm" title="<?php _e('Confirm delete'); ?>">
    <?php _e('Contact(s) will be permanently deleted. Are you sure?')?></p>
</div>

