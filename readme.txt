=== WC Vendors ===
Contributors: wcvendors, digitalchild
Tags: woocommerce, vendor, shops, product vendor, product vendors, seller
Donate link: http://www.wcvendors.com/
Author URI: http://www.wcvendors.com/
Plugin URI: http://www.wcvendors.com/
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.7.5
License: GPLv2 or later

The free multi-vendor plugin for WooCommerce.  Now you can allow anyone to open a store on your site!

== Description ==
Create your own marketplace and allow vendors to sell just like etsy, Envato, or Amazon type sites! This allows other users to sell tangible products, virtual products, or downloads on your site. With this plugin, your vendors receive commissions you set on products they sell from your store.

= Features =
* Instantly pay vendors their commission as soon as an order is placed
* Or, pay commission on a schedule. Weekly, biweekly, monthly, or manually.
* Vendors can submit products for admin review
* Vendors can view live sales and reports for their products
* Vendors can comment on their orders (eg, to inform customers of a tracking number)
* Vendors can export their orders to a CSV file

== Installation ==
1. Download and install from WordPress.org.
2. Configure as you see fit, under WooCommerce / WC Vendors.
3. Configure email notifications under WooCommerce / Settings
4. View Commissions under WooCommerce / Commissions and WooCommerce / Reports / WC Vendors
5. For more help, visit WCVendors.com and say hello on the community forums.

== Frequently Asked Questions ==

= What version of WooCommerce do you support ? =

Woocommerce 2.1 or above is supported.

= What version of php has been tested ? =

PHP 5.4 has been tested. 

= Where do I get help ? =

You can post a support question on the support tab, however you'll get more help over at our community forums (http://www.wcvendors.com)

== Screenshots ==

1. General options - Configure default commission, how members can register and more.
2. Product options page - allows you to hide specific options on the add new product window from vendors. 
3. Capabilities options - restrict what your vendors can see and do. 
4. Pages options - configure what pages will load the relevant vendor templates. These can be customised. 
5. User paypment info - define how your vendors get paid and when. 
6. WC Vendors Paypal Adaptive payments setup.
7. Email template options for the relevant WC Vendor emails. 

== Upgrade Notice ==
No Upgrade required at this time.

== Changelog ==

= Version 1.7.5 - 23rd June 2015 = 

* Merged: Check product post type in vendor dashboard thanks simplementNat
* Updated: Base language file
* Updated: Compatibility for Shipment Tracking for v1.3.5 #167
* Fixed: Shipping taxes 
* Fixed: Pending Products for Vendors #168
* Added: Vendor shipping override #171 
* Added: Give Tax Per Vendor Override #56
* Added: Hide duplicate product option
* Fixed: Email firing for pending status only
* Updated: Unified vendor-main/mini-header variables
* Fixed: Email template paths to woocommerce paths
* Merged: Updated Brazilian Portuguese thanks carlosramosweb
* Added: Seller Info to header #161
* Updated: Spanish Translations #160
* Updated: Brazilian Portuguese Language #156 

= Version 1.7.4 - 26th May 2015 =

* Added: Mark shipped filter #157
* Fixed: Added Tax total to vendor email #146
* Updated: Location of email templates in theme to wc-vendors/emails
* Added: User email to Vendor Display Options #158
* Fixed: Mass Pay Now Bug #159
* Fixed: Mark as shipped for downloadable product #40
* Added:  Brazilian Portuguese language #156 
* Updated: Default Language file
* Fixed: Translation issue for query test #155
* Updated: Template base for emails
* Fixed: Vendor email and renamed template #135
* Fixed: Better CSV Output #63
* Fixed: Made PayPal optional on Vendor Dashboard Shop Settings #144
* Update: fixed return query var
* Fixed: Test for product post types #149
* Fixed: 2.1 Depreciated return call
* Fixed: PHP Strict static call in commissions class
* Merged: Is Vendor checks all user roles #147 thanks crabilld

= Version 1.7.3 - 13th may 2015 = 

* Fixed: Paypal AP IPN url issue

= Version 1.7.2 - May 8th 2015 = 

* Added: Filters for seller tab #141
* Fixed: URI Too Large Error #143
* Fixed: Give tax to vendors #142
* Updated: Spanish Translations #140
* Added: Persian Translation #139

= Version 1.7.1 - April 24th 2015 = 

* Fixed: Invalid argument on new orders dashboard page #138
* Updated: Base translation file

= Version 1.7.0 - April 23rd 2015 = 

* Fixed: add_query_arg/remove_query_arg XSS issue
* Fixed: Hide Notice not working for admin settings
* Added: Shop Settings page in WordPress dashboard
* Added: Orders page in WordPress dashboard 

= Version 1.6.2 - April 17th 2015 = 

* Added: Option to change sold by vendor name #106
* Fixed: Error notice in vendor dashboard #133
* Fixed: Pagination in commissions admin screen #68
* Added: Support for WooCommerce Order Status Manager
* Fixed: Updated media filter method for vendors #132
* Fixed: Commission not logged for variations #131

= Version 1.6.1 - April 10th 2015 =

* Fixed: Support for Per Product Shipping 2.2.x #126
* Added: Filter to change commission label in vendor email #127

= Version 1.6.0 - April 8th 2015 = 

* Added: Admin notices for vendor page slug & permalinks
* Fixed: Plugin row meta links
* Added: Upgrade notice
* Fixed: Rounding issue #120
* Fixed: Paypal https host check depreciated call
* Added: show_products attribute #107
* Updated: Text in denied template to make more sense when registration disabled #123
* Updated: wcv_vendorslist shortcode now shows 3 column output #123
* Fixed: Index issue #122
* Updated: New plugin and template directory structure - IMPORTANT READ KB

= Version 1.5.0 - March 11th 2015 = 

* Added: Spanish translation thanks Mauricio
* Added: French translation thanks JP
* Added: CSS class for sold by (classes same as filters in those files)
* Fixed: Paypal return URL 
* Added: Vendor Dashboard UI Improvements
* Added: WC Vendors Test Gateway
* Updated: ToolTips to be more helpful
* Added: Admin option for not giving shipping cost to vendor
* Fixed: Disable notify admin 
* Fixed: Mark as shipped/unshipped 
* Fixed: Duplicate column name 

= Version 1.4.5 - February 22nd 2015 = 

* Updated: select2 3.5.2 for settings api
* Fixed: Replaced Chosen with Select2 #102
* Fixed: Table Rate Shipping issue #103
* Fixed: Featured column issue #100
* Updated: Filter call for report
* Fixed: Call to depreciated function #98

= Version 1.4.4 - February 17th 2015 =

* Fixed: Hardcoded table in wcv_vendorslist shortcode 

= Version 1.4.3 - February 16th 2015 =

* Fixed: Placeholder on Product Reports

= Version 1.4.2 - February 13th 2015 =

* Added: Commission status sort to commissions page 
* Fixed: Recent Commissions limit of 20 now works on selected date range
* Fixed: Report By product in WC2.3 
* Fixed: Vendor Report date selector in wp-admin 
* Fixed: Tracking plugin Order Meta
* Added: New filter wcvendors_dashboard_google_maps_link
* Fixed: Formatting error for Google maps link
* Added: New actions in vendor-dashboard wcvendors_vendor_unship, wcvendors_vendor_ship (thanks Nathan H) 

= Version 1.4.1 - January 30th, 2015 =

* Fixed: Language file loading issue 
* Fixed: Static function calls in commission class for php 5.6
* Fixed: Static call in Vendor Cart 
* Added: New language files for de_AT, de_DE (thanks to theHubi), it_IT (thanks to Nicole)
* Added: New actions for main and mini headers (before and after see KB)

= Version 1.4.0 - January 16th, 2015 = 

* Added: product category + vendor shortcode [wcv_product_category category="category" vendor="vendorname"]
* Added: Tracking number support via WooThemes Shipment Tracking plugin
* Added: Google Maps for delivery address on front end
* Fixed: woocommerce_wp_text_input via merged pull request from svenl77
* Added: Vendor List shortcode [wcv_vendorlist] + template for styling see KB for full details 
* Fixed: Report not showing Commission by Product
* Fixed: Paths in language files

= Version 1.3.1 - December 23, 2014 =

* Fixed: Sold by in invoices 

= Version 1.3.0 - December 22, 2014 =
 
* Added: show vendor on all emails #29
* Fixed: Critical issue #58
* Added: Vendor header templates #65
* Added: Vendor to QuickEdit #12
* Fixed: Updating notices to use 2.1 Notice API #62
* Added: wcvendors_registration_checkbox filter to denied.php template view
* Added: wcvendors_vendor_registration_checkbox filter to filter "Apply to become a vendor?" at registration.
* Added: wcvendors_vendor_registration_checkbox to filter "Apply to become a vendor?"

= Version 1.2.0  - November 14, 2014 =
 
* Added new filters to change sold by text see Knowledge base for details
* Added sold by to product loop for archive-product.php, see knowledge base on how to disable or change this
* Added new option to hide "Featured product" from vendors
* Added Sold By Filter as per #3
* Removing unused tag filter
* Updated default.pot 
* Fixing attribute bug #48 - Thanks to gcskye
* Removing legacy translations
* Fixed Orders view errors
* Fixing call to incorrect method #45

= Version 1.1.5 - October 29, 2014 =

* Fixed orders view to remove incorrect call to woocommerce print messages

= Version 1.1.4 (First release on WordPress.org) - October 14, 2014 =

* Resolved shipping bug
* Commission totals are now properly displayed on the WooCommerce / WC Vendors / Payments tab
* Number of internal bug fixes

= Version 1.1.3 (Initial Public Release) - August 09, 2014 =

* Numerous bug fixes
* New Shortcodes:  These new shortcodes are based on the WooCommerce included shortcodes.  They have been modified to show output based on the vendor you specify.  All other arguments to the shortcodes from WooCommerce will also work on these shortcodes.

          Recent Products Shortcode
          [wcv_recent_products vendor="VENDOR-LOGIN-NAME" per_page=3]

          Products Shortcode
          [wcv_products vendor="VENDOR-LOGIN-NAME"]

          Featured Products Shortcode
          [wcv_featured_products vendor="VENDOR-LOGIN-NAME"]

          Sale Products
          [wcv_sale_products vendor="VENDOR-LOGIN-NAME"]

          Top Rated Products on sale
          [wcv_top_rated_products vendor="VENDOR-LOGIN-NAME"]

          Best Selling Products on sale
          [wcv_best_selling_products vendor="VENDOR-LOGIN-NAME"]

== Upgrade Notice ==

= 1.6.0 =
Our template system has been upgraded.  See (http://www.wcvendors.com/knowledgebase/v1-5-0-to-v1-6-0-upgrade-guide/) for the upgrade guide if you have used our templates to change how your site looks.
