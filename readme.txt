=== Easy Digital Downloads - Frontend Shipping ===

Author URI: http://astoundify.com
Plugin URI: https://github.com/Astoundify/edd-frontend-shipping/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=contact@appthemer.com&item_name=Donation+for+Astoundify
Contributors: Astoundify, SpencerFinnell
Tags: easy digital downloads, edd, edd shipping, frontend shipping, astoundify, marketify
Requires at least: 4.1
Tested up to: 4.2.1
Stable Tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Output a list of orders/payments for the current vendor when using Easy Digital Downloads + Frontend Submissions + Simple Shipping

== Description ==

Provides an easy way for vendors to see all orders that contain items they own/sell. Outputs shipping address information so vendors can ship physical goods. Splits shipped and unshipped orders into separate tables for easier browsing.

= Required Plugins =

This plugin will only work if Easy Digital Downloads, Frontend Submissions, and Simple Shipping are installed and active.

= Collecting Rates =

This is a simple plugin meant to fill a gap as well as possible until the next version of Frontend Submissions is released. This plugin does not allow shipping prices to be assigned automatically upon submission. There are two ways to handle it:

* Add two fields: Domestic and International to collect the "suggested" shipping price for the item. Then as the admin manually transfer these values to the shipping fields as you approve products.
* Don't add the fields and control the shipping yourself (still on a per-product basis).

= Frontend Submission Dashboard =

The page also cannot be directly integrated into the FES tabbed menu. You can edit the welcome message on the dashboard to link to the page that you have added the `[edd_frontend_shipping]` shortcode to, however.

= Irrelevancy when Frontend Submissions 2.2 is released =

When Frontend Submissions 2.2 is released this plugin will become irrelevant. Frontend Submissions and Simple Shipping are both recieving updates that will make this plugin useless.

= Viewing Payment Receipts =

The way this plugin works is as follows: A link is provided to the users' purchase receipt to view products that have been purchased (so they can be shipped). The purchase receipt becomes available to vendors who's products are in the users' cart. This means if a cart contains items from multiple vendors all vendors will be able to see the receipt.

= Where can I use this? =

Any theme that uses Easy Digital Downloads, Frontend Submissions, and Simple Shipping. Astoundify has released a theme that does just that. Check out ["Marketify"](themeforest.net/item/marketify-digital-marketplace-wordpress-theme/6570786?ref=Astoundify)

== Frequently Asked Questions ==

= How do I view the orders? =

Add the shortcode `[edd_frontend_shipping]` to the page you want vendors to be able to view their orders.

== Installation ==

1. Install and Activate
2. Create a WordPress page and add the Frontend Shipping shortcode: `[edd_frontend_shipping]`

== Changelog ==

= 1.0: March 18, 2014 =

* First official release!
