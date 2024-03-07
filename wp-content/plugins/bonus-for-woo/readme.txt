=== Bonus for Woo ===
Contributors: calliko
Donate link: https://sobe.ru/na/kompyuti_na_podderzhku_plagina_computy_for_plugin_support
Tags: loyalty program, loyalty,  bonus, cashback, bonus, points, reward, referral system, referral,
Requires at least:  4.9
Tested up to:  6.3
WC requires at least: 5.0
WC tested up to: 8.0.1
Stable tag: 5.7.1
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html


== Description ==

This plugin is designed to create a bonus system with cashback.
The cashback percentage is calculated based on the users' status in the form of bonus points.
Each user status has a corresponding cashback percentage.
The users' status depends on the total amount of the users orders.
Cashback is accumulated in the client's virtual wallet.

[youtube https://www.youtube.com/watch?v=7pzkT8bmY8c&ab_channel=AlexanderTokmakov]

== Free plugin features ==
* Points for product reviews.
* Integer and decimal points
* Hide the ability to spend points for discounted items.
* Show the history of bonus points.
* Email notifications.
* Export and import points.
* Shortcodes.

== Additional settings for the PRO version ==
* Points on your birthday.
* Daily points for the first login.
* Points for registration.
* Exclude categories of products that cannot be purchased with cashback points.
* Exclude payment method.
* Exclude items that cannot be purchased with Cashback Points.
* Minimum order amount to redeem points.
* Withdrawal of bonus points for inactivity.
* Referral system.
* Coupons.

== Testing ==
You can test the plugin on [**this page**](https://demo.tastewp.com/bonus-for-woo)

== SUPPORT ==
If you need support or have questions, please write to our [**support**](https://wordpress.org/support/plugin/bonus-for-woo/) or [**blog**](https://computy.ru/blog/bonus-for-woo-wordpress/#reply-title).

== Installation ==

1. Upload dir `bonus-for-woo-computy` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Options in adminpanel
2. View plugin on userpage
3. View the number of points received on the product page.
4. Calculation by points in the basket and checkout.
5. History of accrual of points in the client's account
6. User edit page.
7. E-mail notification settings page.
8. Coupon management page.
9. Statistics page

== Frequently Asked Questions ==

 
= When are points awarded? =
When the administrator marks the order as "Completed"

= Why are points not awarded immediately after the order is paid? =
This is done on purpose to exclude the case when the client has spent the earned
points, but decided to return the last product. In this case, the bonus balance goes negative.

= When are points deducted? =
When the customer confirms the order in checkout.

= Possible cashback is not displayed in the shopping cart and checkout. =
You are most likely logged in with an administrator account. Administrators are not involved
in the loyalty system.



== Changelog ==
= 5.7.1 - 2023-08-19 =
* Fixed bug

= 5.7.0 - 2023-08-15 =
* Code optimization
* Added a setting for the PRO version: From what date to count the amount of orders. Thanks, @mishaml
* Added filter bfw-cart-cashback-display-amount. Thanks, @mishaml
* Fixed bugs
* Fixed "Cannot redeclare" error
* Fixed error saving shortcode [bfw_account]

= 5.6.1 - 2023-07-17 =
* Fixed a bug where points were deducted when a coupon was activated.
* Fixed a critical error when recording the history of accrual of points through cron.
* Fixed a bug where the number of days for burning points in the personal account was not displayed.
* Changed plugin activation from file_get_content to curl
* Woocommerce 7.8.2 compatible
* Added filter bfw-update-points-filter. Thanks, @mishaml
* Added filter bfw-excluded-products-filter. Thanks, @mishaml

= 5.6.0 - 2023-05-22 =
* Removed connection between statuses and user roles.
* Fixed bonus points redemption calculation.
* In translations, a setting has been added for the "Use Points" button.

= 5.5.1 - 2023-05-10 =
* Added the ability to search for clients by email when exporting.
* Added a range of dates to the history of accrual of bonuses.
* Fixed an issue where points were debited when using shipping tax.
* Fixed incorrect display of possible points using third party fees.

= 5.5.0 - 2023-05-08 =
* Added shortcode: [bfw_account_referral] displaying a block of information of the referral system from the account.
* Added an order status setting at which points will be debited.
* Fixed a bug with calculating the minimum amount in the cart and checkout.
* Fixed a bug in bonus points statistics.
* Fixed work function get_current_endpoint. Thanks, @mishaml
* Fixed the ability to write off taxes.
* Fixed a bug with the interaction of coupons and bonus points.
* Code optimization
* WordPress 6.2 compatible
* Woocommerce 7.6 compatible


= 5.4.3 - 2023-03-30 =
* Fixed adaptation of the bonus points list table.
* Fixed a bug with displaying possible points for redemption in the shopping cart.
* Fixed deletion of the first line in the history of accrual of points, in the user editor.
* Fixed endpoints for orders in the history of bonus points.
* Woocommerce 7.5.1 compatible
* Code optimization

= 5.4.2 - 2023-03-06 =
* Fixed bugs
* Fixed email recording error when purchasing a plugin.
* Fixed error displaying statistics.

= 5.4.1 - 2023-03-02 =
* Added lists of those invited by the referral system in the user editor.

= 5.4.0 - 2023-03-01 =
* Added a status name in the user editor.
* Added an order status setting at which points will be returned.
* Added the second level of the referral system.

= 5.3.4 - 2023-02-14 =
* Fixed the ability to remove the use of bonuses if coupons are applied.

= 5.3.3 - 2023-01-30 =
* File import error fixed

= 5.3.2 - 2023-01-25 =
* Fixed the possibility of debiting cashback from the delivery amount.

= 5.3.1 - 2023-01-17 =
* Fixed bugs

= 5.2.3 - 2023-01-15 =
* Added a button to remove points in the subtotal.
* Added the ability to ignore discounts from cashback accrual.
* Added the ability to change the bonus system based on coupons.
* Fixed hiding the "Up to" display of a possible bonus point for the shortcode.
* Code optimization

= 5.2.1 - 2022-12-29 =
* Fixed hiding "Up to" near the display of a possible bonus point.
* Fixed critical error when returning goods.
* Fixed bugs in the rules and conditions generator.
* Fixed bugs in the user's personal account.

= 5.2.0 - 2022-12-16 =
* Added database check for existence during plugin activation.
* Added daily accrual of bonus points for logging into your account.
* Code refactoring

= 5.1.2 - 2022-12-05 =
* Fixed hiding the history of accrual of bonus points for customers.
* Fixed a bug in bonus points statistics.

= 5.1.1 - 2022-11-10 =
* Added shortcode for displaying referral link
* Woocommerce 7.1.0 compatible
* WordPress 6.1 compatible

= 5.1.0 - 2022-09-23 =
* Fixed: check for email existence before sending notification.
* Added link to the terms and conditions in the client's personal account.
* Added computy copyright in the client account.
* Added the ability to create an offline order.

= 5.0.1 - 2022-09-19 =
* Woocommerce 6.9.2 compatible
* Added link on documentation
* Statistics Calculation Optimization
* Code optimization

= 5.0.0 - 2022-09-11 =
* Added bonus system description generator.
* Added a shortcode for displaying the entire personal account [bfw_account]
* Added a copy icon on the product page.
* Added a hook for sending a message with a custom method.
* Added a notification in the basket about how many total points are in the account.
* Fixed a small security hole
* Fixed the correct accrual of points for a delayed product review.
* Fixed the ability to disable the redemption of points for discounted products.
* Fixed: check for email existence before sending notification.
* Moved the menu in the admin panel to a separate tab.
* Moved the birthday field to the top.
* Database update



