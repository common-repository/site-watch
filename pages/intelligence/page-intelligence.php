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

$active_tab = EZP_IBC_U::get_request_val('tab', 'click-triggers');

$nonce_action = "$active_tab-tab";

if(isset($_REQUEST['action']))
{
    check_admin_referer($nonce_action);
}
?>
<style>
    #easy-pie-ibc-intelligence-tab-control i { color: #aaa; margin-left:5px; }
</style>

<div id="easy-pie-ibc-intelligence-tab-control" class="wrap">

    <?php screen_icon(EZP_IBC_Constants::PLUGIN_SLUG); ?>
    <h2>
        <?php                 
            echo __('Intelligence') . ' - Site Spy';
            
            $add_slug = null;
            $page_trigger_help = '';
            $click_trigger_help = '';
            $user_events_help = '';
            $scoring_help = '';
            
            if($active_tab == 'page-triggers')
            {
                $add_slug = EZP_IBC_Constants::$PAGE_TRIGGER_SLUG;
                $add_text = __('Add Trigger');   
                $text = __('User Event is triggered when user hits page(s).');
                
                $page_trigger_help = "<i title='$text' class='fa fa-question-circle'></i>";
            }
            else if($active_tab == 'click-triggers')
            {
                $add_slug = EZP_IBC_Constants::$CLICK_TRIGGER_SLUG;
                $add_text = __('Add Trigger');
                
                $text = __('User Event is triggered when user clicks element(s).');
                
                $click_trigger_help = "<i title='$text' class='fa fa-question-circle'></i>";
            }
            else if ($active_tab == 'types')
            {
                $add_slug = EZP_IBC_Constants::$USER_EVENT_TYPE_SLUG;
                $add_text = __('Add Event');
                
                $text = __('Use in combination with Triggers to know when users are interacting with site in meaningful ways (landing pages, special articles, post groups etc...)');
                
                $user_events_help = "<i title='$text' class='fa fa-question-circle'></i>";
            } 
            else
            {
                $text = __('How much to add to user score when event is triggered');
                
                $scoring_help = "<i title='$text' class='fa fa-question-circle'></i>";
            }
                    
            if($add_slug != null)
            {
                 _e("<a href='?page=$add_slug' class='add-new-h2'>$add_text</a>");
            }                          
        ?>
    </h2>

    <div id="easypie-cs-options" class="inside">
        <h2 class="nav-tab-wrapper">  
            <a href="?page=<?php echo EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG . '&tab=click-triggers' ?>" class="nav-tab <?php echo $active_tab == 'click-triggers' ? 'nav-tab-active' : ''; ?>"><?php echo __('Click Triggers') . $click_trigger_help; ?></a>
            <a href="?page=<?php echo EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG . '&tab=page-triggers' ?>" class="nav-tab <?php echo $active_tab == 'page-triggers' ? 'nav-tab-active' : ''; ?>"><?php echo __('Page Triggers') . $page_trigger_help; ?></a>            
            <a href="?page=<?php echo EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG . '&tab=types' ?>" class="nav-tab <?php echo $active_tab == 'types' ? 'nav-tab-active' : ''; ?>"><?php echo __('User Events') . $user_events_help; ?></a>
            <a href="?page=<?php echo EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG . '&tab=scoring' ?>" class="nav-tab <?php echo $active_tab == 'scoring' ? 'nav-tab-active' : ''; ?>"><?php echo __('Scoring') . $scoring_help; ?></a>
        </h2>
        <form id="easy-pie-cs-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$INTELLIGENCE_SUBMENU_SLUG . '&tab=' . $active_tab); ?>" >             
            <?php wp_nonce_field($nonce_action) ?>
            <div id='tab-holder'>
                <?php                
                if ($active_tab == 'page-triggers')
                {
                    include 'page-page-triggers-tab.php';
                }
                else if ($active_tab == 'click-triggers')
                {
                    include 'page-click-triggers-tab.php';
                }
                else if ($active_tab == 'types')
                {
                    include 'page-user-event-types-tab.php';
                }
                else
                {
                    include 'page-intelligence-scoring-tab.php';
                }
                
                ?>
            </div>           

            <input type="hidden" id="ezp-cs-submit-type" name="ezp-cs-submit-type" value="save"/>

            <?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

