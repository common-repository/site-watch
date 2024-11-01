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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/class-ezp-ibc-list-control.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-list-entity.php');

$nonce_action = "page-lists";

// Determine if the list control threw any actions on postback
if (isset($_REQUEST['action'])) {
    check_admin_referer($nonce_action);
    
    $action = $_REQUEST['action'];

    if ($action == 'delete') {
        if (isset($_REQUEST['list_id'])) {
            $list_id = $_REQUEST['list_id'];

            
            EZP_IBC_List_Entity::delete_by_id($list_id);
        }
    }
}
?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/lists/page-list.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
</style>

<div class="wrap">
    
<?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <?php
        
    
    $list_control = new EZP_IBC_List_Control($nonce_action);
    $list_control->prepare_items();
      
    $list_subtype_text = 'All Lists';
    ?>
    <div class="wrap">

        <ul class="subsubsub">
            <li class="all"><a href="#" class="current"><?php echo "$list_subtype_text " ?></a></li>
        </ul>
     
            
            <div id="easypie-cs-options" class="inside">
            <?php $list_control->display(); ?>            
            </div>

        </div>
    </div>
</div>
