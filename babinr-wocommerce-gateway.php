<?php
/**
 * @package Babinr_Wocommerce_Gateway
 * @author    Ashwin
 * @category  Admin
 * @copyright Copyright (c) 2015-2016, Babinr and WooCommerce
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 * @version 0.0.3
 */

/*
 * Plugin Name: Woocommer Payment Gateway By Babin (Ashwin) Rana
 * Plugin URI: https://github.com/ashwinrana/nabil-woocommer-payment-gateway
 * Description: Create Own Payment Gateway Plugin.
 * Author: Babin (Ashwin) Rana
 * Author URI: https://babinr.com.np
 * Version: 0.0.3
 * 
 * Copyright: © 2009-2015 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

 // Check If Request is Comming From Wordpress Or Not.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Make sure WooCommerce is Installed And Active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

// Add the gateway to WC Available Gateways
function add_wc_babinr_gateway_class( $methods ) {
	$methods[] = 'WC_Babinr_Gateway';
	return $methods;
}

add_action( "template_redirect", "response_handler" );
add_filter( 'woocommerce_payment_gateways', 'add_wc_babinr_gateway_class' );

add_action( 'plugins_loaded', 'init_babinr_wc_gateway_class' );

// Plugin load and Ask for Payment Class
function init_babinr_wc_gateway_class() {
    require_once dirname( __FILE__ ) . '/includes/WC_Babinr_Gateway.php';
}

//Use to redirect the page after payment process is completed.
function response_handler(){
	if(isset($_GET['orderID'])){
		global $woocommerce, $wp;
		$url = home_url($wp->request);
		$order = wc_get_order( $_GET['orderID'] );
		if($order != null){
			$quantity = $order->get_item_count();
			if($url == get_site_url() . '/accept'){
				$order->payment_complete();
				$order->reduce_order_stock();
				$woocommerce->cart->empty_cart();
				$order->add_order_note( 'Hey, your order is paid! Thank you!', true );
				$url = $order->get_checkout_order_received_url();
				wp_safe_redirect( $url );
				exit();
			}
			if($url == get_site_url() . '/decline'){
				$order->add_order_note( 'Card has been declined by the bank', false );
				$order->update_status( 'failed' );
			}
			if($url == get_site_url() . '/cancel'){
				$order->add_order_note( 'Transaction has been canceled by the user', false );
				$order->update_status( 'cancelled' );
			}
		}else{
			status_header( 404 );
	        nocache_headers();
	        include( get_query_template( '404' ) );
	        exit();
		}
	}
}
