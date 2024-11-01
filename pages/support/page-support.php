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
require_once(EZP_IBC_U::$PLUGIN_DIRECTORY . '/classes/Entities/class-ezp-ibc-global-entity.php');

$nonce_action = "page-support";

if (isset($_REQUEST['action']))
{
    check_admin_referer($nonce_action);
}

$global = EZP_IBC_Global_Entity::get_instance();

$logo_url = EZP_IBC_U::$PLUGIN_URL . '/images/EZPie_large_logo3.png';

?>

<script type="text/javascript" src='<?php echo EZP_IBC_U::$PLUGIN_URL . "/js/support/page-support.js?" . EZP_IBC_Constants::PLUGIN_VERSION; ?>'></script>

<style lang="text/css">
    #easy-pie-help-instructions a { text-decoration: inherit; }
    #easy-pie-help-instructions li { margin-bottom: 18px; }
    #easy-pie-help-instructions h2 { margin-top:35px; font-weight:bold}
    #easy-pie-help-instructions h3 { font-weight:normal}
    #easy-pie-help-instructions p { font-size:1.2em; margin-top:2px;}
</style>

<div class="wrap">

    <h2><?php echo __('Support') . ' - Site Spy'; ?></h2>

    <div id="easypie-cs-options" class="inside">

        <form id="easy-pie-ibc-main-form" method="post" action="<?php echo admin_url('admin.php?page=' . EZP_IBC_Constants::$SUPPORT_SUBMENU_SLUG); ?>" >             
            <?php wp_nonce_field($nonce_action) ?>

            <div class="wrap" style="margin-top:25px">               

                <div id="easypie-ibc-options" class="inside">
                    <?php
                    $action_updated = null;

                    $error_string = '';

                    if (isset($_POST['action']) && $_POST['action'] == 'save')
                    {
                        if (isset($_POST['_execution_auditing_on']))
                        {
                            //$_POST['execution_auditing_on'] = 1;
                            EZP_IBC_Options_U::set_option(EZP_IBC_Option_Subkeys::Execution_Auditing_On, 1);
                        }
                        else
                        {
                            //$_POST['execution_auditing_on'] = 0;
                            EZP_IBC_Options_U::set_option(EZP_IBC_Option_Subkeys::Execution_Auditing_On, 0);
                        }

                        $error_string = $global->set_post_variables($_POST);

                        if ($error_string == '')
                        {
                            EZP_IBC_U::debug("saving global");
                            $global->save();
                            $action_updated = true;
                        }
                        else
                        {
                            EZP_IBC_U::debug("there is an error saving global $error_string ?");
                        }
                    }
                    ?>

                    <input type="hidden" name="action" value="save"/>            
                    <?php
                    if ($error_string != '') :
                        ?>
                        <div id="message" class="error below-h2"><p><?php echo EZP_IBC_U::__('Errors present:') . "<br/> $error_string" ?></p></div>
                    <?php endif; ?>

                    <?php if ($action_updated) : ?>
                        <div id="message" class="updated below-h2"><p><span><?php echo EZP_IBC_U::__('Settings Saved.'); ?></span></p></div>
                    <?php endif; ?>

                    <div id="easy-pie-help-instructions" class="postbox" style="padding: 11px">

                        <img style="margin-bottom:20px; height:96px;" src="<?php echo $logo_url; ?>"/>
                        <h2 style="margin-top:0px;">Not Sure How to Use Site Spy?</h2>
                        <h3><a href="http://easypiewp.com/site-watch-help/" target="_blank">Check out the Help Documentation</a></h3>
                        
                        <h2>Found a Bug?</h2>
                        <h3>
                            <ol style="margin-top:5px">
                                <li>Enable auditing below</li>
                                <li>Enable <a href="http://easypiewp.com/wordpress-error-log-your-friend/" target="_blank">error logging</a> if possible</li>
                                <li>Retry the failing operation</li>
                                <li>Download and <a href="mailto:bob@easypiewp.com?subject=Bug Report" target="_blank">send us the error log</a></li>
                            </ol>
                        </h3>
                        
                        
                        <h2>Have a Suggestion or Problem?</h2>
                        <p>Let us know if you're having a problem or think Site Spy could be better - <span style="font-style:italic">Your feedback is highly appreciated.</span></p>
                        <h3><a href="mailto:bob@easypiewp.com?subject=Easy Pie Site Spy Feedback" target="_blank">Shoot us an email</a></h3>

                    </div>

                    <div class="postbox" >
                        <div class="inside" >  
                            <h3><?php _e('Logging'); ?></h3>
                            <table class="form-table"> 
                                <tr>
                                    <th scope="row" style='height:50px;'>
                                        <?php echo _e("Execution Auditing") ?>
                                    </th>
                                    <td>
                                        <div class="compound-setting">                            
                                            <input type="checkbox" <?php EZP_IBC_U::echo_checked((bool) EZP_IBC_Options_U::get_option(EZP_IBC_Option_Subkeys::Execution_Auditing_On)); ?> name="_execution_auditing_on" ><?php EZP_IBC_U::_e('Enabled'); ?></input>
                                            <div class="description-div"><span class="description"><?php echo _("Dumps detailed execution information to the error log"); ?></span></div>
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" style="height:35px">
                                        <?php echo _e("Error Log") ?>
                                    </th>
                                    <td>
                                        <button onclick="easyPie.IBC.Support.PageSupport.getDebugFile();
                                                return false;"><?php _e('Download'); ?></button>
                                    </td>
                                </tr>                        
                            </table>

                        </div>
                    </div>


                    <?php
                    submit_button();
                    ?>
                </div>

            </div>           

            <?php EZP_IBC_U::echo_footer_links(); ?>        
        </form>
    </div>
</div>

