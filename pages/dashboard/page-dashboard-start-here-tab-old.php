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

<style lang="text/css">    
    #easy-pie-start-here p, #easy-pie-start-here ul, #easy-pie-start-here h4 { font-size: 1.1em }
    #easy-pie-start-here ul { padding-left: 48px; list-style-type:disc }
</style>

<div style="margin-top:24px; max-width:670px">

    <div>
        <?php
        echo EZP_IBC_Storage_Manager::get_alert_text();
        ?>        
    </div>


    <div>
        <div id="easy-pie-start-here" class="postbox" >
            <div class="inside" >
                <form style="float:right; margin-right: 15px;" method="POST">
                    <input type="hidden" name="enable_start" value="0" />                
                    <button type="submit" ><?php _e('Disable Tab'); ?></button>
                </form>                
                <h2>Quick Start</h2>
                <p>More complete help can be found in the <a href="#todo">Help Documentation.</a></p>
                <h2>Know What Individual Visitors Are Doing</h2>
                <p>PeopleMetrics lets you know what people are doing on your site at an individual level. This is different than standard analytics packages that tell you what anonymous groups have been doing on your site.</p>
                <p>Once you know what individuals are doing, you can send individuals with communications targeted to their interests. Knowing behavior at the individual level lets you improve your site based on insights into individual behaviors.</p>
                <h2>Core Concepts</h2>
                <h3>Contacts</h3>
                <p>Contacts represent anonymous and identified visitors.</p>
                <h4>Lifecycle</h4>
                <p>Contacts are in one of three "lifecycle" states.</p>
                <ul>
                    <li><strong>Prospects.</strong> Prospects are contacts who have never submitted identifying information. Although prospects are unidentified, the system uses a cookie to identify them and track history across visits.</li>
                    <li><strong>Leads.</strong> Leads are contacts that have submitted some identifying information. This usually means they have either submitted an email form or logged into the WordPress Admin panel.</li>
                    <li><strong>Customers.</strong> Customers are contacts who have purchased at least one product using a supported and configured eCommerce system. At this time WooCommerce is supported with more systems planned.</li>
                </ul>
                <h4>Event History</h4>
                <p>Full history of a contact is retained through the life of the contact record. This means you'll know what Leads and Customers were doing even when they were anonymous Prospects.</p>
                <h3>Events</h3>
                <p>The standard events are: Form Submission, Lifecycle Change, Login, Page Load, and Purchase.</p>
                <h4>User Events</h4>
                <p>As their name suggests, User Events are defined by you and have specific meaning to your site.</p>
                <h3>Triggers</h3>
                <p>Page and Click Triggers are used to fire User Events when users view a page or click an element that you've specified.</p>
                <p>Details on Triggers and User Events are found in the <a href="#todo">Help Documentation</a>.
                <h2>A Few Usages</h2>
                <p>Some of the things you could want to measure and improve using PeopleMetrics:</p>
                <ul>
                    <li>What people do after hitting a landing page</li>
                    <li>Who’s been returning to your site over and over while still not giving you their email address (transitioning to a lead)</li>
                    <li>Which people are interested in your product's "Feature A" and which are interested in "Feature B"</li>
                    <li>Who went so far as the billing page but decided not to buy</li>
                    <li>What external links are clicked on and who clicked them</li>
                    <li>Who added an extra item to their cart but changed their mind and removed it</li>
                    <li>...and an endless number of site-specific scenarios</li>
                </ul>
                <p>The following example illustrates how to use the system using a many of the system features. If you wanted to know more details go to the <a href="#todo">help documentation.</a></p>                                                
                <h2>Example: Know Who's Interested in a Product Feature Set</h2>
                <p>In this example, we want to find out which people are interested in "Feature Set A" of our product. The more we can know about a contact without having to rely on them to submit information, the better since most people don't want to spend time giving feedback.</p>
                <h3>Configuration</h3>
                <h4>Step 1. Create User Event “Feature Set X”</h4>
                </p>First let's create a user event that will be a market when someone has done something relating to “Feature Set X” of our product. That’s as simple as going to the User Events tab and clicking “Add”.</p>
                <h4>Step 2. Create Page Trigger for Feature Set X</h4>
                <p>Now that we have a User Event set up for Feature Set X, we’re going to want to link the set of pages related to that feature to the User Event.</p>
                <p>So we go into the Page Triggers tab, and click “Add”. After this we have the choice of linking pages based on WordPress pages, WordPress post ids, or a relative URL.</p>
                <p>In this example let’s select a couple WordPress pages.</p>
                <h4>Step 3. Create Click Trigger for Feature Set X</h4>
                <p>Now that we have our page trigger set up, let’s configure a Click Trigger to capture a button click.  A Click Trigger is a way for us to have the system generate a User Event when someone clicks a web page element.</p>
                <p>In this example let’s add a button on the home page to this Click Trigger since clicking the button causese a popup to appear that lets them know a bit about “Feature X”.</p>
                <p>The elements that generate the User Event are identified by a jQuery selector. If you're unfamiliar with jQuery selectors or need some brushing up, check out <a href="#todo">Identifying Elements using jQuery</a>.</p>
                <h4>Step 4. Create List “People Interested in Feature Set X”</h4>
                <p>Finally, we’re going to create a list that will contain people who have shown some decent interest in “Feature Set X”.</p>
                <p>OK that’s it for the configuration.  Let’s recap where we’re at and what the concepts are here.  We want to see which people who visit our site are interested in “Feature X” of our product. We’ve identified a couple pages related to that feature and wired up a Page Trigger and also identified a button related to the feature and wired up a Click Trigger.</p>
                <p>Now, when people interact with the site, we’ll see the User Event “Interested in Feature X” as shown below, in the realtime dashboard and event history tabs:</p>
                <p>On realtime dashboard (above)</p>
                <p>In contact event history(above)</p>
                <h3>Creating a list of people interested in “Feature Set X”</h3>
                <p>Now that we’ve configured the User Event and triggers, and have let the system run for a while, let’s create a list out of the people who have shown interest in this feature</p>                
                <h4>Step 1. Go to contacts tab</h4>
                <p>First we go to the contacts tab and bring up a list of all users.</p>
                <h4>Step 2. Set filter based on event “Feature Set X”</h4>
                <p>Click the ‘show advanced options’ and then choose “Feature Set X”. This will set the filter to bring up those users who’ve generated the Feature Set X event.</p>
                <p>We want to also only show Leads since they are the only ones that we can actually contact at this point (Note: There may be scenarios where you want to include prospects - if you’re evaluating why people aren’t becoming leads for instance)</p>
                <h4>Step 3. Add the people who generated the most events list “People Interested in Feature Set X”</h4>
                <p>Now it’s a matter of adding people to the list.</p>
                <h2>Conclusion</h2>
                <p>In conclusion, you see from this example that we can really start digging into people’s individual behavior, filtering them and grouping them based on that behavior for action or research into their behavior.</p>
            </div>
        </div>
    </div>
</div>
