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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/class-ezp-ibc-event-list-control.php');

$action_updated = null;
$event_type = -1;
$search = null;

if (isset($_REQUEST['s']))
{
    $search = trim($_REQUEST['s']);

    if ($search == '')
    {
        $search = null;
    }
}

if (isset($_REQUEST['event']))
{
    $event_type = trim($_REQUEST['event']);

    if ($event_type == '')
    {
        $event_type = -1;
    }

    $r = $_REQUEST['event'];

}

// Determine if the list control threw any actions on postback
if (isset($_REQUEST['action']))
{
    $action = $_REQUEST['action'];

    if ($action == 'delete')
    {
        if (isset($_REQUEST['event_id']))
        {
            $event_id = $_REQUEST['event_id'];

            EZP_IBC_Event_Entity::delete_by_id($event_id);
        }
    }
}

//if ($query_info == null)
//{
//    $stage_text = $stage == -1 ? __('All Contacts') : EZP_IBC_Report_Helper::get_stage_text($stage);
//    $event_text = $event == -1 ? '' : EZP_IBC_Report_Helper::get_event_text($event, $event_parameter);
//    $event_range_text = $event_range == -1 ? 'a' : EZP_IBC_Report_Helper::get_range_text($event_range);
//
//    if ($event != -1)
//    {
//        $who = __('who');
//        $list_subtype_text = "$stage_text $who $event_text $event_range_text";
//    } else
//    {
//        $list_subtype_text = "$stage_text";
//    }
//} else
//{
//    $list_subtype_text = $query_info->title;
//}


$event_list_control = new EZP_IBC_Event_List_Control($contact_id, $event_type, $search, $nonce_action);
$event_list_control->prepare_items();

$event_text = '';

switch ($event_type)
{
    case -1:
        $event_text = __('All Events');
        break;

    default:
        $event_text = EZP_IBC_Event_Entity::get_type_string($event_type);
        break;
}

?>

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
    .easy-pie-details-value { color: darkred}
    .easy-pie-details-label { color: darkgreen}
</style>

<div class="wrap">
    <ul class="subsubsub">
        <li class="all"><a href="#" class="current"><?php echo $event_text ?></a></li>
    </ul>
    <form method="post">
        <input type="hidden" name="page" value="event_list_control" />
        <?php $event_list_control->search_box('search', 'search_id'); ?>
    </form>

    <div id="easypie-cs-options" class="inside">
        <?php $event_list_control->display(); ?>            
    </div>
</div>