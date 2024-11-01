=== Site Spy ===
Contributors: bobriley
Donate link: http://easypiewp.com/donate/
Tags: analytics, click tracking, lead scoring, lead tracking, Hubspot, lead generation, visitor tracking, woocommerce, contact management, CRM, contacts, javascript events, inbound marketing, newsletter
Requires at least: 3.5
Tested up to: 4.1.0
Stable tag: 0.5.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Tells you who's visiting your site and how valuable they are. Uses event tracking, lead capture and lead scoring. Supports WooCommerce.

== Description ==
Site Spy is a powerful visitor tracking, capture and scoring tool that allows you to better target your audience and customers.

**Who's Viewing, Who's Clicking.. Even Who's Making a WooCommerce Purchase!**

Site Spy's event system lets you know exactly who's done what, from visiting a page, to clicking a button to purchasing a product and more.

**Capture Leads With Existing Forms**

Using the forms already on your site, Site Spy captures critical contact information of site visitors, allowing you to easily contact them in the future.

**Use Lead Scoring to Find Valuable Visitors**

Lead Scoring allows you to determine how valuable each visitor is to your business.

Each site action is weighted by importance as defined by you. Site Spy then keeps a running total of these weights as visitors interact with your site. This allows you to know exactly who are the most engaged and most likely to purchase.

**Overview Video**


[youtube https://www.youtube.com/watch?v=w_C_sRl_PEg]


= Features =
* **Event History.** Tracks visitor event history across multiple visits and computers.
* **Lead Capture.** Detects all form submissions, capturing names and email addresses.
* **Lead Scoring.** Set importance of each event to know which leads are most interested.
* **Real-time Monitor.** See who's doing what on your site *right now*. 
* **Lifecycle State.** Tracks current state of users: prospect, lead, or customer.
* **Traffic Source.** Know exactly how each contact originally found your site.
* **Contact Lists.** Organize visitors into lists based on search criteria.
* **Contact Export.** Export contacts as CSV for easy import into email marketing services.

= Events =
* **Pageview.** Describes a single page view.
* **WooCommerce Purchase.** Line item information from a item purchase.
* **Page Element Click.** Details of page element that was clicked.
* **Lifecycle Transition.** What lifecycle transition took place.
* **JavaScript Event**. Inject event using a page element handler or JavaScript function.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Site Spy'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `site-watch.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =
1. Download `site-watch.zip`
2. Extract the `site-watch` directory to your computer
3. Upload the `site-watch` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin's dashboard

== Frequently Asked Questions ==

= How Does Lead Scoring Work? =

Lead scoring is a simple but powerful way to know which leads are most valuable.

You first mark certain site activities as having higher or lower importance. Site Spy then uses this configuration to change visitor scores as they interact with your site. This is best understood via example:

**Example**

We'll configure the following:

* **Page View** = 1 point
* **Form Submission** = 10 points.
* **Call-to-Action Button Click** = 20 points.

Two leads then visit the site and do the following:

**Lead 1** views 3 pages, submit 2 forms then clicks the call-to-action button.

**Lead 2** views 5 pages, submits 1 form then leaves.

Site Spy scores them:

**Lead 1**: (3 x 1 point + 2 x 10 points + 20 points) = **Score of 43 points**.

**Lead 2**: (5 x 1 point +  1 x 10 points) = **Score of 15 points**.

From score alone, it's apparent that Lead 1 is 'worth' more because they've participated in more important activities than Lead 2.

== Screenshots ==
1. Site Status 
2. Real-time Monitor
3. Editing Contact
4. Event History
5. Click Triggers

== Changelog ==

= 0.5.0 =
* Initial release

= 0.5.1 =
* Renamed from Site Watch to Site Spy
* Added lead scoring
* Bug fixes

= 0.5.2 =
* Small bug fixes
* Usability improvements

= 0.5.3 =
* Renamed to Site Spy

== Upgrade Notice ==

= 0.5.0 =
Initial release

= 0.5.1 =
Site Watch is now known as "Site Spy".  
We now include Lead Scoring which allows you to specify how important each visitor activity is. This allows you to quickly determine which leads are most ready to purchase.
We've also fixed several bugs, including contact export.

= 0.5.2 =
Small bug fixes and usability improvements

= 0.5.3 =
Important - renamed to "Lead Watch" to "Site Spy" so be sure to look for the new name in your Admin menu
