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
//require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-ecommerce.php');
//require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/class-ezp-ibc-report-helper.php');
?>

<style>
    #easy-pie-realtime-status { display: inline!important;}
    #easy-pie-realtime-outer-div h3 { margin-bottom: 7px; text-align: left}
    #easy-pie-realtime-outer-div .inside { margin: 0; padding: 0 12px 5px}
    #easy-pie-realtime-outer-div th { padding-top: 4px; padding-bottom:8px; font-weight: bold}
    #easy-pie-realtime-outer-div td { padding: 2px;}
    
    #easy-pie-latest-visitors td { text-overflow: ellipsis; white-space:nowrap; overflow:hidden }

    .easy-pie-stats-column { width: 100px!important;}
    .easy-pie-activity-box { border: solid black 1px; }
</style>

<div style="margin-top: 25px;" id="easy-pie-realtime-outer-div">    

    <h3><?php _e("Current Visitors") ?><span id = 'easy-pie-visitor-count'></span></h3>
    <div class="postbox easy-pie-activity-box" style="margin-bottom:30px; height:156px" >            
        <div class="inside" >            
<!--            <table style="width:875px;" class="form-table easy-pie-stats-table"> 
                <tr>
                    <td>-->
                        <table class='form-table' id="easy-pie-latest-visitors"><tr><td><?php _e('Retrieving...'); ?></td></tr></table>
<!--                    </td>
                </tr>                                                               
            </table>-->
        </div>
    </div>

    <h3>
        <?php echo ("Activity"); ?>
    </h3>
    <div class="postbox easy-pie-activity-box" style="margin-bottom:15px; height:350px; overflow:auto;">        
        <div class="inside">
            <table class="form-table" id="easy-pie-ibc-latest-events"><tr><td><?php _e('Retrieving...'); ?></td></tr></table>
        </div>
    </div>

    <div style='margin-bottom:30px;'>
        <button style="padding: 5px; display:inline-block" onclick="easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.togglePlay(false);"><i style="cursor:pointer" id='easy-pie-pause-icon' class="fa fa-pause fa-2x"></i></button>
        <button style="padding: 5px; margin-left:10px; display:inline-block" onclick="easyPie.IBC.Dashboard.PageDashboard.RealtimeMonitorTab.togglePlay(true);"><i style="cursor:pointer" id='easy-pie-play-icon' class="fa fa-play fa-2x"></i></button>
    </div>

</div>
<div style="display:none;" id="easy-pie-realtime-monitor-detail-dialog">
</div>