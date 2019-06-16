<?php
/**
 * @package Babinr_Wocommerce_Gateway
 * @version 0.0.1
 */

class WC_Babinr_Gateway extends WC_Payment_Gateway {

	public function __construct() {
		$this->id = "babinr_gateway";
		$this->icon = apply_filters('woocommerce_offline_icon', '');
		$this->has_fields = false;
		$this->method_title = "Babinr Payment Gateway";
		$this->method_description = "Create Your Own WoCommerce Payment Gateway Plugin";
		$this->init_form_fields();
		$this->init_settings();
		$this->title = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	// Initialise Gateway Settings Form Fields
	function init_form_fields() {
	     $this->form_fields = array(
	     	'enabled' => array(
		        'title' => __( 'Enable/Disable', 'woocommerce' ),
		        'type' => 'checkbox',
		        'label' => __( 'Enable Babinr Payment', 'woocommerce' ),
		        'default' => 'yes'
		    ),
		     'title' => array(
		          'title' => __( 'Title', 'woocommerce' ),
		          'type' => 'text',
		          'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		          'default' => __( 'Babinr Payment', 'woocommerce' )
		    ),
		     'description' => array(
		          'title' => __( 'Description', 'woocommerce' ),
		          'type' => 'textarea',
		          'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
		          'default' => __("Pay with your credit card via our super-cool payment gateway", 'woocommerce')
		    ),
		    'testmode' => array(
				'title'       => 'Test mode',
				'label'       => 'Enable Test Mode',
				'type'        => 'checkbox',
				'description' => 'Place the payment gateway in test mode using test API keys.',
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'test_api_key' => array(
				'title'       => 'Test API Key',
				'type'        => 'text',
				'description' => 'Place The Pagment Gateway Test API Key.',
				'desc_tip'    =>  true,
			),
			'live_api_key' => array(
				'title'       => 'Live API Key',
				'type'        => 'text',
				'description' => 'Place The Pagment Gateway Live API Key.',
				'desc_tip'    =>  true,
			)
	    );
	}

	function payment_fields()
      {
         if ( $this->description ) 
            echo wpautop(wptexturize($this->description));
      }

	//Payment checkout page
	function process_payment( $order_id ) {

	    global $woocommerce;
	    $order = new WC_Order( $order_id );

	    // Mark as on-hold
	    // $order->update_status('on-hold', __( 'Awaiting bank card payment', 'woocommerce' ));

	    // Reduce stock levels
	    // $order->reduce_order_stock();

	    // Remove cart
	    // $woocommerce->cart->empty_cart();

	    $this->send_request_to_bank( $order );

	    // Return thankyou redirect
	    // return array(
	    //     'result' => 'success',
	    //     'redirect' => $this->get_return_url( $order )
	    // );
	}

	public function webhook() {

		var_dump('Here in side the webhook function');
		$order = wc_get_order( $_GET['id'] );
		$order->payment_complete();
		$order->reduce_order_stock();
	 
		// update_option('webhook_debug', $_GET);
	}

	public function response_handler()
	{
		var_dump('Here in response_handler function');
		die();
	}

	// add_action( "template_redirect", "response_handler" );
	add_action( 'woocommerce_api_wc_babinr_gateway', array( $this, 'response_handler' ) );

	function send_request_to_bank( $order = null ) {
		if(!is_null($order)) {
			// print("<pre>" . print_r($order, true) . "</pre>");
		    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		    $address = $order->get_shipping_address_1() . ',' . $order->get_shipping_city() . ',' . $order->get_shipping_state() . ',' . $order->get_shipping_postcode() . ',' . $order->get_shipping_country();
			$ch = null;
    		$token = null;
			if($this->get_option( 'testmode' ) == "no") {
				$token = $this->get_option( 'live_api_key' );
   	 			$ch = curl_init('https://nabil.themenepal.info/testPay');
			}else{
				$token = $this->get_option( 'test_api_key' );
        		$ch = curl_init('https://sandboxnabil.themenepal.info/testPay');
			}

			$params = [
		        'currency' => $order->get_currency(),
		        'amount' => $order->get_total(),
		        'name' => $name,
		        'address' => $address,
		        'email' => $order->get_billing_email(),
		        'phone' => $order->get_billing_phone(),
		        'description' => $order->id,
		        'orderID' => $order->id,
		        'approved' => get_site_url() . '/accept',
		        'canceled' => get_site_url() . '/cancel',
		        'declined' => get_site_url() . '/decline',
		        'token' => $token,
		    ];

		    curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		    if (!$result = curl_exec($ch)) {
		        trigger_error(curl_error($ch));
		    }
		    curl_close($ch);
		    return $result;
		}
	}
}