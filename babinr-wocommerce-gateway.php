<?php
/**
 * @package Babinr_Wocommerce_Gateway
 * @author    Ashwin
 * @category  Admin
 * @copyright Copyright (c) 2015-2016, Babinr and WooCommerce
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 * @version 0.0.1
 */

/*
 * Plugin Name: Woocommer Payment Gateway By Babin (Ashwin) Rana
 * Plugin URI: https://babinr.com.np
 * Description: Create Own Payment Gateway Plugin.
 * Author: Babin (Ashwin) Rana
 * Author URI: https://babinr.com.np
 * Version: 0.0.2
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

function response_handler(){
	global $woocommerce, $wp;
	$url = home_url($wp->request);
	if($url == get_site_url() . '/accept'){
		$order = wc_get_order( $_GET['orderID'] );
		$order->payment_complete();
		$order->reduce_order_stock();
		// update_option('webhook_debug', $_GET);
		wp_safe_redirect( $this->get_return_url( $order ) );
		exit;

	}
	if($url == get_site_url() . '/decline'){
		// wp_safe_redirect( $url );
		// exit;
	}
	if($url == get_site_url() . '/cancel'){
		// wp_safe_redirect( $url );
		// exit;
	}
	
	// if(isset($_GET['orderID'])){
	// 	$order = wc_get_order( $_GET['orderID'] );
	// 	print_r($order . '<pre>');
	// 	die();
	// $order->payment_complete();
	// $order->reduce_order_stock();
 
	// update_option('webhook_debug', $_GET);
	// wp_safe_redirect( $this->get_return_url( $order ) );
	// exit;
	// }
}
add_filter( 'woocommerce_payment_gateways', 'add_wc_babinr_gateway_class' );

add_action( 'plugins_loaded', 'init_babinr_wc_gateway_class' );

// Plugin load and Ask for Payment Class
function init_babinr_wc_gateway_class() {
    require_once dirname( __FILE__ ) . '/includes/WC_Babinr_Gateway.php';
}
