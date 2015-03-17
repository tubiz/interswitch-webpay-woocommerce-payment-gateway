=== Interswitch Webpay Woocommerce Payment Gateway ===
Contributors: tubiz
Donate link: http://bosun.me/donate
Tags: woocommerce, payment gateway, payment gateways, mastercard, visa cards, mastercards, interswitch, verve cards, tubiz plugins, verve, nigeria, webpay
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Interswitch Webpay Woocommerce Payment Gateway allows you to accept payment on your Woocommerce store via Interswitch Webpay payment gateway.





== Description ==

This is a Interswitch Webpay payment gateway for Woocommerce.


To signup for Interswitch Webpay visit their website by clicking [here](https://connect.interswitchng.com/documentation/getting-started/)

Interswitch Webpay Woocommerce Payment Gateway allows you to accept payment on your Woocommerce store using Nigeria issued Visa Card, Mastercard and Verve Cards

With this Interswitch Webpay Woocommerce Payment Gateway plugin, you will be able to accept the following payment methods in your shop:

* __MasterCards__
* __Visa Card__
* __Verve Cards__

= Note =

This plugin is meant to be used by merchants in Nigeria.

= Plugin Features =

*   __Accept payment__ via Verve Cards, Visa Cards and Mastercards.
* 	__Seamless integration__ into the WooCommerce checkout page.
* 	__Add Naira__ currency symbol. To select it go to go to __WooCommerce > Settings__ from the left hand menu, then click __General__ from the top tab. From __Currency__ select Naira, then click on __Save Changes__ for your changes to be effected.

= Premium Addons =

**Interswitch Webpay WooCommerce Payment Gateway Transaction Log**

[Interswitch Webpay WooCommerce Payment Gateway Transaction Log](https://tunspress.com/plugins/interswitch-webpay-woocommerce-payment-gateway-transaction-log/) plugin log and save the full details of every payment notification that happens on your site when using the Interswitch Webpay Woocommerce Payment Gateway Plugin.

*Some Features Include*

*	This plugin logs each payment transaction that is made via the Interswitch Webpay Woocommerce Payment Gateway plugin in your WordPress website.
*	It also allows you to view the full details of each transaction that happens on your WordPress site.
*	You can also search for transaction via it's transaction id.
* 	Plus much more. <br />
To get the plugin click [here](https://tunspress.com/plugins/interswitch-webpay-woocommerce-payment-gateway-transaction-log/)


= Suggestions / Feature Request =

If you have suggestions or a new feature request, feel free to get in touch with me via the contact form on my website [here](http://bosun.me/get-in-touch/)

You can also follow me on Twitter! **[@tubiz](http://twitter.com/tubiz)**


= Contribute =
To contribute to this plugin feel free to fork it on GitHub [Interswitch Webpay Woocommerce Payment Gateway on GitHub](https://github.com/tubiz/interswitch-webpay-woocommerce-payment-gateway)


== Installation ==

= Automatic Installation =
* 	Login to your WordPress Admin area
* 	Go to "Plugins > Add New" from the left hand menu
* 	In the search box type "Interswitch Webpay Woocommerce Payment Gateway"
*	From the search result you will see "Interswitch Webpay Woocommerce Payment Gateway" click on "Install Now" to install the plugin
*	A popup window will ask you to confirm your wish to install the Plugin.

= Note: =
If this is the first time you've installed a WordPress Plugin, you may need to enter the FTP login credential information. If you've installed a Plugin before, it will still have the login information. This information is available through your web server host.

* Click "Proceed" to continue the installation. The resulting installation screen will list the installation as successful or note any problems during the install.
* If successful, click "Activate Plugin" to activate it.
* 	Open the settings page for WooCommerce and click the "Payment Gateways," tab.
* 	Click on the sub tab for "Interswitch Webpay".
*	Configure your "Interswitch Webpay" settings. See below for details.

= Manual Installation =
1. 	Download the plugin zip file
2. 	Login to your WordPress Admin. Click on "Plugins > Add New" from the left hand menu.
3.  Click on the "Upload" option, then click "Choose File" to select the zip file from your computer. Once selected, press "OK" and press the "Install Now" button.
4.  Activate the plugin.
5. 	Open the settings page for WooCommerce and click the "Payment Gateways," tab.
6. 	Click on the sub tab for "Interswitch Webpay".
7.	Configure your "Interswitch Webpay" settings. See below for details.



= Configure the plugin =
To configure the plugin, go to __WooCommerce > Settings__ from the left hand menu, then click "Payment Gateways" from the top tab. You should see __"Interswitch Webpay"__ as an option at the top of the screen. Click on it to configure the payment gateway.

__*You can select the radio button next to the Interswitch Webpay from the list of payment gateways available to make it the default gateway.*__

* __Enable/Disable__ - check the box to enable Interswitch Webpay Payment Gateway.
* __Title__ - allows you to determine what your customers will see this payment option as on the checkout page.
* __Description__ - controls the message that appears under the payment fields on the checkout page. Here you can list the types of cards you accept.
* __Product ID__  - enter your Product Identifier for PAYDirect here. This will be given to you by Interswitch.
* __Pay Item ID__  - enter your PAYDirect Payment Item ID here. This will be given to you by Interswitch.
* __Mac Key__  - enter your Mac Key here. This will be given to you by Interswitch
* __Test Mode__  - Tick this to enable test mode, remember to untick this if you are ready to accepting live payment on your site.
* Click on __Save Changes__ for the changes you made to be effected.





== Frequently Asked Questions ==

= What Do I Need To Use The Plugin =

1.	You need to have Woocommerce plugin installed and activated on your WordPress site.
2.	You need to signup for Interswitch Webpay on [Interswitch](https://connect.interswitchng.com/documentation/getting-started/)




== Changelog ==

= 1.0.4 =
* 	Fix: Payment status message not shown if pretty permalinks is disabled
*	Fix: PHP notice when a customer is redirected back from Interswitch

= 1.0.3 =
*	New: Display customer name and transaction id on Interswitch payment page (Interswitch UAT requirement)
* 	Fix: Fix an error that prevents the payment status message from being shown after being redirected back from the gateway

= 1.0.2 =
*   New: Display transaction ID before the customer is forwarded to Interswitch to make payment
*   New: Automatically forward the customer to Interswitch to make payment
* 	New: Add a new hook.
*   Fix: Removed Unirest lib, HTTP request now use wp_remote_get

= 1.0.1 =
* 	Fix: This fix an error querying the details of a transaction

= 1.0.0 =
*   First release





== Upgrade Notice ==

= 1.0.4 =
* Fix for payment status not shown if pretty permalinks is disabled



== Screenshots ==

1. Interswitch Webpay Woocommerce Payment Gateway setting page

2. Test Mode notification, always disaplyed in the admin backend till when test mode is disabled

3. Interswitch Webpay Wooocommerce Payment Gateway method on the checkout page

4. Order confirmation before payment is made

5. Failed Transaction: No Card Record

6. Failed Transaction: PIN tries exceeded

7. Failed Transaction: Insuffcient Funds

8. Successful Transaction