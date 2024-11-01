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

require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/ui-components/class-ezp-ibc-list-member-control.php');
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-list-entity.php');

?>
<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/lists/page-list.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>   

<style lang="text/css">
    .compound-setting { line-height:20px;}
    .narrow-input { width:66px;}
    .long-input { width: 345px;}
    #ezp-name-submit .submit { padding-bottom: 0px;}
</style>

<div class="wrap">
<?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <div class="wrap">
            <input type="hidden" name="form_action" value="save"/>
            <input type="hidden" name="list_id" value="<?php echo $list->id; ?>" />
<?php
if ($error_string != '') :
    ?>
                <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
            <?php endif; ?>

            <?php if ($action_updated) : ?>
                <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('List Updated.'); ?></div>
            <?php endif; ?>

            <div class="postbox" style="margin-top:12px;" >
                <div class="inside" >
                    <table class="form-table"> 
                        <tr>
                            <th scope="row">
<?php echo EZP_IBC_U::_e("Name") ?>
                            </th>
                            <td>
                                <div class="compound-setting">                            
                                    <input class="long-input" name="name" type="text" value="<?php echo $list->name; ?>" />
                                </div>
                            </td>
                        </tr>                            
                    </table>
                    <div style='margin-top:-12px' id='ezp-name-submit'>
                        
                    </div>
                </div>                
            </div>
    </div>
    <?php submit_button(); ?>
</div>