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

$image_dir = EZP_IBC_U::$PLUGIN_URL . '/images/start-here/';
?>

<style lang="text/css">    
    #easy-pie-start-here p, #easy-pie-start-here ul, #easy-pie-start-here h4 { font-size: 1.1em }
    #easy-pie-start-here p { line-height: 2em; margin-top:10px;}
    #easy-pie-start-here h2 { font-weight:bold }
    #easy-pie-start-here ul { padding-left: 48px; list-style-type:disc }
    #easy-pie-start-here li { line-height: 23px; margin-bottom: 11px}
    #easy-pie-start-here h4 { margin: 1em 0}
    #easy-pie-start-here img { max-width: 640px; box-shadow: 1px 7px 26px -5px rgba(34,34,34,1); margin-bottom:37px }
    
    #easy-pie-start-here h2.easy-pie-detail-header { margin-top:25px; margin-bottom: 25px; cursor: pointer; background-color: #ddd; border-radius: 5px; padding: 8px; margin-bottom: 8px; -webkit-box-shadow: 0 10px 6px -6px #777; -moz-box-shadow: 0 10px 6px -6px #777; }
    #easy-pie-start-here h4.easy-pie-detail-header { text-align:center; margin:40px 155px 60px 155px; cursor: pointer; background-color: #eee; border-radius: 5px; padding: 8px; -webkit-box-shadow: 0 10px 6px -6px #777; -moz-box-shadow: 0 10px 6px -6px #777; }
    #example-1-prospect-to-lead { border: solid #eee 1px; border-radius: 5px; font-size: 1.2em; }
</style>

<div style="margin-top:24px; max-width:675px">

    <div>
        <?php
        echo EZP_IBC_Storage_Manager::get_alert_text();
        ?>        
    </div>


    <div>
        <div id="easy-pie-start-here" class="postbox" >
            <div class="inside" >
                <form style="float:right; margin-left:15px; margin-right: 15px;" method="POST">
                    <input type="hidden" name="enable_start" value="0" />                
                    <button type="submit" ><?php _e('Hide Tab'); ?></button>
                </form>                

                <p style="background-color:yellow; width:514px"><strong style='font-style:italic'>Important:</strong> Clear caches of caching plugins when Site Spy is first installed.</p>
                <h2>Why Use Site Spy?</h2>
                <p>Site Spy complements your marketing strategy by allowing you to better target your audience and customers.</p>
                <h3>Know Exactly Who's Doing What on Your Site</h3>
                <p>Site Spy's event system lets you know exactly what people have done what, from visiting a page, to clicking a button to purchasing a product and more.</p>
                <h3>Capture Leads</h3>
                <p>Using the forms already on your site, Site Spy will capture critical contact information of people visiting your site, allowing you to easily contact them in the future.</p>
                <h3>Score Leads</h3>
                <p>Each site action is associated with a score as defined by you. Site Spy keeps a running total of the score of each prospect, lead and customer, allowing you to know exactly who are the most important and ready for a sale.</p>
                <h3>Optimize Your Site</h3>
                <p>Since Site Spy tracks individual visitor history, allowing you to improve your site based on the behavior of identified 'important' visitors.</p>                
                <h2 class="easy-pie-detail-header" onclick="jQuery('#example-1-details').toggle('fast');" style="cursor:pointer; width:312px">Example 1: Capturing Leads</h2>
                <div id="example-1-details" style="display:none" >
                    <p style="margin-top:25px">When a form with an email field is submitted, the email address is recorded and the the contact is marked as a 'Lead'.
                        Standard forms can be used, no special setup is required.</p>
                    <h3>Step 1. Set up a form that collects email</h3>
                    <p>You can use a variety of email contact form plugins, but for this example I'll keep things simple and use a nice little plugin called the <a href="https://wordpress.org/plugins/dreamgrow-scroll-triggered-box/" target="_blank">Dreamgrow Scroll Triggered Box</a>. </p>
                    <img src='<?php echo $image_dir . '0-optin-form-empty.png' ?>' />
                    <h3>Step 2. Wait for Form Submissions</h3>
                    <p>Now that we have a simple email form installed, the system will continually look for submissions.</p>
                    <h4 class="easy-pie-detail-header" onclick="jQuery('#example-1-prospect-to-lead').toggle('fast');">Watch a Prospect Become a Lead</h4>                                
                    <div id="example-1-prospect-to-lead" style="display:none">
                        <p>1. An anonymous user visits and is picked-up by the Real-time Monitor</p>
                        <img src='<?php echo $image_dir . '1-anonymous-pageload.png' ?>' />

                        <p>2. The user fills out the opt-in form</p>
                        <img src='<?php echo $image_dir . '2-optin-form.png' ?>' />

                        <p>3. The Real-time monitor shows the state transition and email address capture</p>
                        <img src='<?php echo $image_dir . '3-lifecycle-change.png' ?>' />
                    </div>

                    <h3>Form Notes</h3>
                    <ul>
                        <li>Site Spy updates the contact's name if a form submits that information.</li>
                        <li>Site Spy attempts to be smart about understanding the different fields on forms, however it can get confused on certain forms. If you notice your form isn't being captured properly <a href="http://easypiewp.com/site-watch-help/" target="_blank">refer to help</a> for tips on properly setting up your form.</li>
                        <li>If you have changed your form and information still isn't saved properly, please <a target="_blank" href="mailto:bob@easypiewp.com">email us</a>.</li>                    
                    </ul>
                </div>
                <h2 class="easy-pie-detail-header" onclick="jQuery('#example-2-details').toggle('fast');" style="cursor:pointer; width:274px">Example 2: Lead Scoring</h2>
                <div id='example-2-details' style='margin-top:25px; display:none'>
                    <p style="background-color:yellow; text-align:center; font-weight:bold;font-style:italic">This is early documentation that will be improved in the future.</p>
                    <p>As users interact with your site, they accumulate a 'score' which is composed of values that have been assigned to different actions. This score helps you determine which contacts are most engaged in your site and are most ready to be sold to.</p>
                    <ul>
                    <li><strong>To define point values of the standard actions:</strong> Go to the "Scoring" tab from the "Intelligence" menu to define how many score points each event is worth.</li>
                    <li><strong>To define point values for user events:</strong> Simply fill in the 'worth' field of each user event type.</li>
                    <li><strong>To determine who are the most valuable contacts:</strong> Use the people dashboard or sort by "Score" on the contact list.</li>
                    </ul>
                </div>
                
                <h2 onclick="jQuery('#example-3-details').toggle('fast');" style="width:612px" class="easy-pie-detail-header">Example 3: Create List of People Clicking External Links</h2>
                <div id="example-3-details" style="display:none">
                    <p style="margin-top:25px">Now lets look at how one can capture clicks on external links. For this example, we'll be monitoring external links on the Easy Pie Tools page.</p>
                    <h3>Configuration</h3>
                    <h4>Step 1. Create a Click Trigger</h4>                                
                    <p>Click Triggers fire User Events when page elements specified with <a href="http://easypiewp.com/selecting-page-elements-with-jquery/" target="_blank">jQuery selectors</a> are clicked.</p>
                    <p>We create a Click Trigger by going to the Click Triggers tab on the Intelligence submenu.</p>
                    <img src='<?php echo $image_dir . '4-add-click-trigger.png' ?>' />                
                    <h4>Step 2. Create a User Event</h4>                                
                    <p>Next we can create a User Event from within the Click Triggers Page by clicking "Create New".</p>
                    <img src='<?php echo $image_dir . '5-create-user-event.png' ?>' />
                    <p>Now we'll name it "Clicked on External Trigger".</p>
                    <img src='<?php echo $image_dir . '6-save-user-event.png' ?>' />
                    <h4>Step 3. Get the Click Trigger Element Selector</h4>                                                
                    <p>We want to monitor external links on the Site Tools page so we'll need to know what those links look like in the page HTML.</p>
                    <p>Opening my handy Chrome browser, I see that all the links of interest are all contained in the article element with id "post-696". The "Coming Soon Pro" link is shown below.</p>
                    <img src='<?php echo $image_dir . '6a-page-internals.png' ?>' />
                    <p>So we need to trigger on all the links (anchors) inside the article with id "post-696". The jQuery selector expression for this is <code>#post-696 a</code>.</p>
                    <img src='<?php echo $image_dir . '7-click-trigger-edit.png' ?>' />
                    <h4>Step 4. Create List</h4>
                    <p>Finally, we’re going to create a list that will later contain people who click on these links.</p>
                    <p>Just go to the Lists Page, add the list <strong>External Link People</strong></p>
                    <img src='<?php echo $image_dir . '8-add-new-list.png' ?>' />
                    <h3>Verifying Things are Working</h3>
                    <p>It’s a good idea at this point to verify the clicks are being captured correctly.</p>
                    <h4>Step 1.</h4>                
                    <p>Click on a couple tools links</p>
                    <img src='<?php echo $image_dir . '8a-click-on-external-links.png' ?>' />
                    <h4>Step 2. Verify User Events show up on Real-time Dashboard</h4>
                    <p>The User Events generated by the clicks should now appear on the Real-time Dashboard:</p>
                    <img src='<?php echo $image_dir . '8b-user-events.png' ?>' />
                    <h3>Wait for Traffic</h3>
                    <p>OK that’s it for the configuration so let’s recap where we’re at. We want to see which people who visit our site have clicked on an external tools link so we created a User Event, then a Click Trigger to connect the click of page links to the User Event and then created a List to store the people who clicked.</p>
                    <p>So at this point it’s a waiting game. Once sufficient time has passed and we  have enough data, we proceed the organization of the data we’ve collected.</p>
                    <h3>Populate the List</h3>
                    <h4>Step 1. Filter Contacts</h4>
                    <p>We want to populate our list with all users who clicked external links. So we click ‘Show Advanced Filter’, then Select the User Event “Clicked on External Tools Link” and click the “Filter” button.</p>
                    <img src='<?php echo $image_dir . '10-filter-by-external-links.png' ?>' />
                    <h4>Step 2. Select contacts and add to list</h4>
                    <p>Now choose the bulk action "Add to External Link People", select who you want added (not shown) and click apply.</p>
                    <img src='<?php echo $image_dir . '11-add-to-external-list.png' ?>' />
                    <p>Now we choose all the contacts, choose the bulk action “Add to Clicked on External Tools Link” and click “Apply”.</p>
                    <h3>Evaluate or Export the List</h3>                
                    <p>Now that we have a list we can a couple general types of things – we could export it for use with a third party mailing list service or we could just use it for our own evaluation, periodically returning to the list to see what those users are up to. </p>
                    <h4>Use for Mailing Lists</h4>
                    <p>Note, that in order to create a mailing list, you’re going to want to ensure only Contacts in the “Lead” or “Customer” lifecycle are included so be sure to either manually select only those types of contacts or set the Contacts filter appropriately.</p>
                    <p>To get the list into a third party email service, go to the Lists Page and click Export on the appropriate list.  You can also export from the List Members tab.</p>                
                    <img src='<?php echo $image_dir . '12-export-list.png' ?>' />
                </div>
            </div>
        </div>
    </div>
</div>
