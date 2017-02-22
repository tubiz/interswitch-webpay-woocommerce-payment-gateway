<?php
/*
	Plugin Name: Interswitch Webpay WooCommerce Payment Gateway
	Plugin URI: http://bosun.me/interswitch-webpay-woocommerce-payment-gateway
	Description: Interswitch Webpay WooCommerce Payment Gateway allows you to accept payment on your Woocommerce store via Verve Card, Visa Card and MasterCard.
	Version: 4.0.0
	Author: Tunbosun Ayinla
	Author URI: http://bosun.me/
	License:           GPL-2.0+
 	License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 	GitHub Plugin URI: https://github.com/tubiz/interswitch-webpay-woocommerce-payment-gateway
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'plugins_loaded', 'tbz_wc_interswitch_webpay_init', 0 );

function tbz_wc_interswitch_webpay_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

	/**
 	 * Gateway class
 	 */
	class WC_Tbz_Webpay_Gateway extends WC_Payment_Gateway {

		public function __construct() {

			$this->id 						= 'tbz_webpay_gateway';
    		$this->icon 					= apply_filters( 'woocommerce_webpay_icon', plugins_url( 'assets/images/interswitch.png' , __FILE__ ) );
			$this->has_fields 				= false;

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Define user set variables
			$this->title 					= $this->get_option( 'title' );
			$this->description 				= $this->get_option( 'description' );
			$this->product_id				= trim( $this->get_option( 'product_id' ) );
			$this->pay_item_id				= trim( $this->get_option( 'pay_item_id' ) );
			$this->mac_key					= $this->get_option( 'mac_key' );
			$this->mac_key 					= preg_replace( '/\s+/', '', $this->mac_key );
			$this->testmode					= $this->get_option( 'testmode' );

        	$this->payment_page 			= $this->get_option( 'payment_page' ) === 'new' ? 'new' : 'old';

        	$this->old_testurl 				= 'https://sandbox.interswitchng.com/webpay/pay';
        	$this->new_testurl 				= 'https://sandbox.interswitchng.com/collections/w/pay';

        	$this->testurl             		= $this->payment_page === 'new' ? $this->new_testurl : $this->old_testurl;

			$this->old_liveurl 				= 'https://webpay.interswitchng.com/paydirect/pay';
			$this->new_liveurl 				= 'https://webpay.interswitchng.com/collections/w/pay';

        	$this->liveurl             		= $this->payment_page === 'new' ? $this->new_liveurl : $this->old_liveurl;

			$this->payment_url 				= $this->testmode === 'yes' ? $this->testurl : $this->liveurl;

			$this->old_test_query_url 		= 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json';
			$this->new_test_query_url 		= 'https://sandbox.interswitchng.com/collections/api/v1/gettransaction.json';

			$this->test_query_url 			= $this->payment_page === 'new' ? $this->new_test_query_url : $this->old_test_query_url;

			$this->old_live_query_url 		= 'https://webpay.interswitchng.com/paydirect/api/v1/gettransaction.json';
			$this->new_live_query_url 		= 'https://webpay.interswitchng.com/collections/api/v1/gettransaction.json';

			$this->live_query_url 			= $this->payment_page === 'new' ? $this->new_live_query_url : $this->old_live_query_url;

			$this->query_url 				= $this->testmode === 'yes' ? $this->test_query_url : $this->live_query_url;

			$this->redirect_url        		= WC()->api_request_url( 'WC_Tbz_Webpay_Gateway' );
        	$this->method_title     		= 'Interswitch Webpay';
        	$this->method_description  		= 'MasterCard, Verve Card and Visa Card accepted';

			//Actions
			add_action( 'woocommerce_receipt_tbz_webpay_gateway', array( $this, 'receipt_page' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			// Payment listener/API hook
			add_action( 'woocommerce_api_wc_tbz_webpay_gateway', array( $this, 'check_webpay_response' ) );

			//Display Transaction Reference on checkout
			add_action( 'before_woocommerce_pay', array( $this, 'display_transaction_id' ) );

			// Check if the gateway can be used
			if ( ! $this->is_valid_for_use() ) {
				$this->enabled = false;
			}
		}

		/**
	 	* Check if the store curreny is set to NGN
	 	**/
		public function is_valid_for_use() {

			if( ! in_array( get_woocommerce_currency(), array('NGN') ) ) {
				$this->msg = 'Interswitch Webpay doesn\'t support your store currency, set it to Nigerian Naira &#8358; <a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=general">here</a>';
				return false;
			}

			return true;
		}


		/**
		 * Check if this gateway is enabled
		 */
		public function is_available() {

			if ( $this->enabled == "yes" ) {

				if ( ! ( $this->product_id && $this->pay_item_id && $this->mac_key ) ) {
					return false;
				}
				return true;
			}

			return false;
		}


        /**
         * Admin Panel Options
         **/
        public function admin_options() {
            echo '<h3>Interswitch Webpay</h3>';
            echo '<p>Interswitch Webpay allows you to accept MasterCard, Verve and Visa payment.</p>';

			if ( $this->is_valid_for_use() ){
	            echo '<table class="form-table">';
	            $this->generate_settings_html();
	            echo '</table>';
            }
			else{	 ?>
			<div class="inline error"><p><strong>Interswitch Webpay Payment Gateway Disabled</strong>: <?php echo $this->msg ?></p></div>

			<?php }
        }


	    /**
	     * Initialise Gateway Settings Form Fields
	    **/
		function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title' 		=> 'Enable/Disable',
					'type' 			=> 'checkbox',
					'label' 		=> 'Enable Interswitch Webpay Payment Gateway',
					'description' 	=> 'Enable or disable the gateway.',
            		'desc_tip'      => true,
					'default' 		=> 'yes'
				),
				'title' => array(
					'title' 		=> 'Title',
					'type' 			=> 'text',
					'description' 	=> 'This controls the title which the user sees during checkout.',
        			'desc_tip'      => false,
					'default' 		=> 'Interswitch Webpay'
				),
				'description' => array(
					'title' 		=> 'Description',
					'type' 			=> 'textarea',
					'description' 	=> 'This controls the description which the user sees during checkout.',
					'default' 		=> 'Accepts Mastercard, Verve Card and Visa Card'
				),
				'payment_page' => array(
					'title'       	=> 'Payment Page',
					'type'        	=> 'select',
					'description' 	=> 'The old payment page is still being used for old merchant.<br>Make sure you place a order to make sure you are redirected to the Interswitch payment page successfully.',
					'options'		=> array(
						''			=> 'Select One',
						'old'		=> 'Old Payment Page',
						'new'		=> 'New Payment Page'
					)
				),
				'product_id' => array(
					'title' 		=> 'Product ID',
					'type' 			=> 'text',
					'description' 	=> 'Product Identifier for PAYDirect.' ,
					'default' 		=> '',
	    			'desc_tip'      => false
				),
				'pay_item_id' => array(
					'title' 		=> 'Pay Item ID',
					'type' 			=> 'text',
					'description' 	=> 'PAYDirect Payment Item ID' ,
					'default' 		=> '',
        			'desc_tip'      => false
				),
				'mac_key' => array(
					'title' 		=> 'Mac Key',
					'type' 			=> 'text',
					'description' 	=> 'Your MAC Key' ,
					'default' 		=> '',
        			'desc_tip'      => false
				),
				'testing' => array(
					'title'       	=> 'Gateway Testing',
					'type'        	=> 'title',
					'description' 	=> '',
				),
				'testmode' => array(
					'title'       	=> 'Test Mode',
					'type'        	=> 'checkbox',
					'label'       	=> 'Enable Test Mode',
					'default'     	=> 'no',
					'description' 	=> 'Test mode enables you to test payments before going live. <br />If you ready to start receving payment on your site, kindly uncheck this.',
				)
			);
		}


		/**
		 * Get Webpay Args for passing to Interswitch
		**/
		function get_webpay_args( $order ) {

			$order_total	= $order->get_total();
			$order_total    = $order_total * 100;

			$product_id 	= $this->product_id;
			$pay_item_id 	= $this->pay_item_id;
			$product_id 	= $this->product_id;
			$mac_key 		= $this->mac_key;

            $redirect_url 	= $this->redirect_url;

			$txn_ref 		= uniqid();
			$txn_ref 		= $txn_ref.'_'.$order->id;

        	$customer_name	= $order->billing_first_name. ' ' . $order->billing_last_name;

			$hash 			= $txn_ref.$product_id.$pay_item_id.$order_total.$redirect_url.$mac_key;
			$hash 			= hash("sha512", $hash);

			// webpay Args
			$webpay_args = array(
				'product_id' 			=> $product_id,
				'amount' 				=> $order_total,
				'currency' 				=> 566,
				'site_redirect_url' 	=> $redirect_url,
				'txn_ref' 				=> $txn_ref,
				'hash' 					=> $hash,
				'pay_item_id' 			=> $pay_item_id,
				'cust_name'				=> $customer_name,
				'cust_name_desc'		=> 'Customer Name',
				'cust_id'				=> $txn_ref,
				'cust_id_desc'			=> 'Transaction Reference',
			);

			WC()->session->set( 'tbz_wc_webpay_txn_id', $txn_ref );

			$webpay_args = apply_filters( 'woocommerce_webpay_args', $webpay_args );

			return $webpay_args;
		}

	    /**
		 * Generate the Webpay Payment button link
	    **/
	    function generate_webpay_form( $order_id ) {

			$order = wc_get_order( $order_id );

			$webpay_args = $this->get_webpay_args( $order );

			// before payment hook
            do_action( 'tbz_wc_webpay_before_payment', $webpay_args );

			$webpay_args_array = array();

			foreach ($webpay_args as $key => $value) {
				$webpay_args_array[] = '<input type="hidden" name="'.esc_attr( $key ).'" value="'.esc_attr( $value ).'" />';
			}

			wc_enqueue_js( '
				$.blockUI({
						message: "' . esc_js( __( 'Thank you for your order. We are now redirecting you to Interswitch to make payment.', 'woocommerce' ) ) . '",
						baseZ: 99999,
						overlayCSS:
						{
							background: "#fff",
							opacity: 0.6
						},
						css: {
							padding:        "20px",
							zindex:         "9999999",
							textAlign:      "center",
							color:          "#555",
							border:         "3px solid #aaa",
							backgroundColor:"#fff",
							cursor:         "wait",
							lineHeight:		"24px",
						}
					});
				jQuery("#submit_webpay_payment_form").click();
			' );

			return '<form action="' . $this->payment_url . '" method="post" id="webpay_payment_form">
					' . implode( '', $webpay_args_array ) . '
					<!-- Button Fallback -->
					<div class="payment_buttons">
						<input type="submit" class="button alt" id="submit_webpay_payment_form" value="Pay via Interswitch Webpay" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">Cancel order &amp; restore cart</a>
					</div>
					<script type="text/javascript">
						jQuery(".payment_buttons").hide();
					</script>
				</form>';
		}

	    /**
	     * Process the payment and return the result
	    **/
		function process_payment( $order_id ) {

			$order 	= wc_get_order( $order_id );

	        return array(
	        	'result' => 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
	        );
		}


	    /**
	     * Output for the order received page.
	    **/
		function receipt_page( $order ) {
			echo '<p>' . __( 'Thank you - your order is now pending payment. You should be automatically redirected to Interswitch to make payment.', 'woocommerce' ) . '</p>';
			echo $this->generate_webpay_form( $order );
		}


		/**
		 * Verify a successful Payment!
		**/
		function check_webpay_response() {

			if( ! empty( $_POST['txnref'] ) || ! empty( $_REQUEST['txnRef'] ) ) {

				if( isset( $_POST['txnref'] ) ) {
					$txnref 		= $_POST['txnref'];
				}

				if( isset( $_REQUEST['txnRef'] ) ) {
					$txnref 		= $_REQUEST['txnRef'];
				}

				$order_details 	= explode('_', $txnref);
				$txn_ref 		= $order_details[0];
				$order_id 		= $order_details[1];

				$order_id 		= (int) $order_id;

		        $order 			= wc_get_order($order_id);
		        $order_total	= $order->get_total();

		        $total          = $order_total * 100;

		        $response       = $this->tbz_webpay_transaction_details( $txnref, $total );

				$response_code 	= $response['ResponseCode'];
				$amount_paid    = $response['Amount'] / 100;
				$response_desc  = $response['ResponseDescription'];

				// after payment hook
                do_action('tbz_wc_webpay_after_payment', $_POST, $response );

				//process a successful transaction
				if( '00' == $response_code ) {

					$payment_ref = $response['PaymentReference'];

					// check if the amount paid is less than the order amount.
					if(  $amount_paid < $order_total ) {

		                //Update the order status
						$order->update_status( 'on-hold', '' );

						//Error Note
						$message = 'Payment successful, but the amount paid is less than the total order amount.<br />Your order is currently on-hold.<br />Kindly contact us for more information regarding your order and payment status.<br />Transaction Reference: '.$txnref.'<br />Payment Reference: '.$payment_ref;
						$message_type = 'notice';

						//Add Customer Order Note
	                    $order->add_order_note( $message, 1 );

	                    //Add Admin Order Note
	                    $order->add_order_note( 'Look into this order. <br />This order is currently on hold.<br />Reason: Amount paid is less than the total order amount.<br />Amount Paid was &#8358;'.$amount_paid.' while the total order amount is &#8358;'.$order_total.'<br />Transaction Reference: '.$txnref.'<br />Payment Reference: '.$payment_ref );

		                add_post_meta( $order_id, '_transaction_id', $txnref, true );

						// Reduce stock levels
						$order->reduce_order_stock();

						// Empty cart
						wc_empty_cart();
					}
					else {

						$message = 'Payment Successful.<br />Transaction Reference: '.$txnref.'<br />Payment Reference: '.$payment_ref;
						$message_type = 'success';

	                	//Add admin order note
	                    $order->add_order_note( 'Payment Via Interswitch Webpay<br />Transaction Reference: '.$txnref.'<br />Payment Reference: '.$payment_ref );

	                    //Add customer order note
	 					$order->add_order_note( 'Payment Successful.<br />Transaction Reference: '.$txnref.'<br />Payment Reference: '.$payment_ref, 1 );

	 					$order->payment_complete( $txnref );

						// Empty cart
						wc_empty_cart();
	                }
				}
				else {
					//process a failed transaction
	            	$message = 	'Payment Failed<br />Reason: '. $response_desc.'<br />Transaction Reference: '.$txnref;
					$message_type = 'error';

					//Add Customer Order Note
                   	$order->add_order_note( $message, 1 );

                    //Add Admin Order Note
                  	$order->add_order_note( $message );

	                //Update the order status
					$order->update_status( 'failed', '' );
				}

			}
			else {

				if( ! empty( $_REQUEST['desc'] ) ) {

					$message = 'Payment Failed. ' . $_REQUEST['desc'];

				} else {

					$message = 	'Payment Failed.';

				}

				$message_type = 'error';

				wc_add_notice( $message, $message_type );

				wp_safe_redirect( wc_get_checkout_url() );

 				exit;

			}

            $notification_message = array(
            	'message'		=> $message,
            	'message_type' 	=> $message_type
            );

			update_post_meta( $order_id, '_tbz_interswitch_wc_message', $notification_message );

            $redirect_url = $this->get_return_url( $order );

            wp_redirect( $redirect_url );

            exit;

		}


		/**
	 	* Query a transaction details
	 	**/
		function tbz_webpay_transaction_details( $txnref, $total ) {

			$product_id 	= $this->product_id;
			$mac_key        = $this->mac_key;

			$url 			= "$this->query_url?productid=$product_id&transactionreference=$txnref&amount=$total";

			$hash 			= $product_id.$txnref.$mac_key;

			$hash 			= hash("sha512", $hash);

			$headers = array(
				'Hash' => $hash
			);

			$args = array(
				'timeout'	=> 90,
				'headers' 	=> $headers
			);

			$response = wp_remote_get( $url, $args );

          	if ( ! is_wp_error( $response ) && 200 == wp_remote_retrieve_response_code( $response ) ) {
				$response = json_decode( $response['body'], true );
          	}
          	else {
          		$response['ResponseCode'] = '400';
          		$response['ResponseDescription'] = 'Can\'t verify payment. Contact us for more details about the order and payment status.';
          	}

          	return $response;
		}


	    /**
	     * Display the Transaction Reference on the payment confirmation page.
	    **/
		function display_transaction_id() {

			if( get_query_var( 'order-pay' ) ){

				$order_id = absint( get_query_var( 'order-pay' ) );
				$order = wc_get_order( $order_id );

				$payment_method =  $order->payment_method;

				if( !isset( $_GET['pay_for_order'] ) && ( 'tbz_webpay_gateway' == $payment_method ) ){
					$txn_ref =$order_id = WC()->session->get( 'tbz_wc_webpay_txn_id' );
					WC()->session->__unset( 'tbz_wc_webpay_txn_id' );
					echo '<h4>Transaction Reference: '. $txn_ref .'</h4>';
				}

			}
		}
	}


	function tbz_wc_interswitch_message() {

		if( get_query_var( 'order-received' ) ){

			$order_id 		= absint( get_query_var( 'order-received' ) );
			$order 			= wc_get_order( $order_id );
			$payment_method = $order->payment_method;

			if( is_order_received_page() &&  ( 'tbz_webpay_gateway' == $payment_method ) ){

				$notification 		= get_post_meta( $order_id, '_tbz_interswitch_wc_message', true );

				$message 			= isset( $notification['message'] ) ? $notification['message'] : '';
				$message_type 		= isset( $notification['message_type'] ) ? $notification['message_type'] : '';

				delete_post_meta( $order_id, '_tbz_interswitch_wc_message' );

				if( ! empty( $message) ){
					wc_add_notice( $message, $message_type );
				}
			}

		}
	}
	add_action( 'wp', 'tbz_wc_interswitch_message', 0 );


	/**
 	* Add Webpay Gateway to WC
 	**/
	function tbz_wc_add_webay_gateway($methods) {
		$methods[] = 'WC_Tbz_Webpay_Gateway';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'tbz_wc_add_webay_gateway' );


	/**
	 * only add the naira currency and symbol if WC versions is less than 2.1
	 */
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) <= 0 ) {

		/**
		* Add NGN as a currency in WC
		**/
		add_filter( 'woocommerce_currencies', 'tbz_add_my_currency' );

		if( ! function_exists( 'tbz_add_my_currency' )){
			function tbz_add_my_currency( $currencies ) {
			     $currencies['NGN'] = __( 'Naira', 'woocommerce' );
			     return $currencies;
			}
		}

		/**
		* Enable the naira currency symbol in WC
		**/
		add_filter('woocommerce_currency_symbol', 'tbz_add_my_currency_symbol', 10, 2);

		if( ! function_exists( 'tbz_add_my_currency_symbol' ) ){
			function tbz_add_my_currency_symbol( $currency_symbol, $currency ) {
			     switch( $currency ) {
			          case 'NGN': $currency_symbol = '&#8358; '; break;
			     }
			     return $currency_symbol;
			}
		}
	}


	/**
	* Add Settings link to the plugin entry in the plugins menu for WC below 2.1
	**/
	if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) <= 0 ) {

		add_filter('plugin_action_links', 'tbz_webpay_plugin_action_links', 10, 2);

		function tbz_webpay_plugin_action_links($links, $file) {
		    static $this_plugin;

		    if (!$this_plugin) {
		        $this_plugin = plugin_basename(__FILE__);
		    }

		    if ($file == $this_plugin) {
	        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Tbz_Webpay_Gateway">Settings</a>';
		        array_unshift($links, $settings_link);
		    }
		    return $links;
		}
	}
	/**
	* Add Settings link to the plugin entry in the plugins menu for WC 2.1 and above
	**/
	else{
		add_filter('plugin_action_links', 'tbz_webpay_plugin_action_links', 10, 2);

		function tbz_webpay_plugin_action_links($links, $file) {
		    static $this_plugin;

		    if (!$this_plugin) {
		        $this_plugin = plugin_basename(__FILE__);
		    }

		    if ($file == $this_plugin) {
		        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=wc_tbz_webpay_gateway">Settings</a>';
		        array_unshift($links, $settings_link);
		    }
		    return $links;
		}
	}


	/**
 	* Display the testmode notice
 	**/
	function tbz_webpay_testmode_notice(){

		$tbz_webpay_settings = get_option( 'woocommerce_tbz_webpay_gateway_settings' );

		$webpay_test_mode = $tbz_webpay_settings['testmode'];

		if ( 'yes' == $webpay_test_mode ) {
	    ?>
		    <div class="update-nag">
		        Interswitch Webpay testmode is still enabled, Click <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=wc-settings&tab=checkout&section=WC_Tbz_Webpay_Gateway">here</a> to disable it when you want to start accepting live payment on your site.
		    </div>
	    <?php
		}
	}
	add_action( 'admin_notices', 'tbz_webpay_testmode_notice' );

}