<?php
/**
 * @package Babinr_Wocommerce_Gateway
 * @version 1.0.1
 */

class WC_Babinr_Gateway extends WC_Payment_Gateway 
{

	public function __construct() {
		$this->id 					= "babinr_gateway";
		$this->icon 				= apply_filters('woocommerce_offline_icon', plugin_dir_url( __DIR__ ) . 'assets/images/payment-gateway.jpeg');
		$this->has_fields 			= false;
		$this->method_title 		= "Babinr Payment Gateway";
		$this->method_description 	= "Your Own Woocommerce Payment Gateway Plugin for Nabil Bank";
		$this->description 			= "Pay Using your Credit Or Debit Card Online.";
		$this->init_form_fields();
		$this->init_settings();
		$this->title 				= $this->get_option( 'title' );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	// Initialise Gateway Settings Form Fields
	public function init_form_fields() {
	     $this->form_fields = array(
	     	'enabled' => array(
		        'title' 	=> __( 'Enable/Disable', 'woocommerce' ),
		        'type' 		=> 'checkbox',
		        'label' 	=> __( 'Enable Babinr Payment', 'woocommerce' ),
		        'default'   => 'yes'
		    ),
		     'title' => array(
		          'title' 		=> __( 'Title', 'woocommerce' ),
		          'type' 		=> 'text',
		          'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
		          'default' 	=> __( 'Babinr Payment', 'woocommerce' )
		    ),
		     'description' 		=> array(
		          'title' 		=> __( 'Description', 'woocommerce' ),
		          'type' 		=> 'textarea',
		          'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
		          'default' 	=> __("Pay with your credit and debit card via our super-cool payment gateway", 'woocommerce')
		    ),
		    'testmode' => array(
				'title'       => 'Test mode',
				'label'       => 'Enable Test Mode',
				'type'        => 'checkbox',
				'description' => 'Place the payment gateway in test mode using test API keys.',
				'default'     => 'yes',
				'desc_tip'    =>  true,
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

	public function payment_fields(){
	    if ( $this->description ) 
	       echo wpautop(wptexturize($this->description));
     }

	public function process_payment( $order_id ) {
	    global $woocommerce;
	    $order = new WC_Order( $order_id );

	    $order->update_status('pending-payment', __( 'Awaiting Card payment', 'woocommerce' ));

  	 	$response_url = $this->send_request_to_bank( $order );

	    return array(
				'result' 	=> 'success',
				'redirect'	=> $response_url
			);
	    
	}	

	public function send_request_to_bank( $order = null ) {
		if(!is_null($order)) {
		    $name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		    $address = $order->get_shipping_address_1() . ',' . $order->get_shipping_city() . ',' . $order->get_shipping_state() . ',' . $order->get_shipping_postcode() . ',' . $order->get_shipping_country();

			$api_url = null;
    		$token = null;

			if($this->get_option( 'testmode' ) == "no") {
				$token   = $this->get_option( 'live_api_key' );
   	 			$api_url = "https://nabilpay.babinr.com.np/testPay";
			}else{
				$token   = $this->get_option( 'test_api_key' );
        		$api_url = "https://sandboxnabilpay.babinrana.com.np/testPay";
			}

			$params = [
		        'currency'    => $order->get_currency(),
		        'timeout'     => 60,
		        'amount'      => $order->get_total(),
		        'name'        => $name,
		        'address'     => $address,
		        'email'       => $order->get_billing_email(),
		        'phone'       => $order->get_billing_phone(),
		        'description' => $order->id,
		        'orderID'     => $order->id,
		        'approved'    => get_site_url() . '/accept',
		        'canceled'    => get_site_url() . '/cancel',
		        'declined'    => get_site_url() . '/decline',
		        'token'       => $token,
		        'woocommerce' => true,
		    ];

		    $response = wp_safe_remote_post($api_url, array(

		    	'body' => $params,

		    ));

		    if ( is_wp_error( $response ) ) {
			    $error_message = $response->get_error_message();
			    echo "Something went wrong: $error_message";
			} else {
			   $var = $response['body'];
			   $var = json_decode($var);
			   return $var->url;
			   exit;
			}	   
		}
	}
}
