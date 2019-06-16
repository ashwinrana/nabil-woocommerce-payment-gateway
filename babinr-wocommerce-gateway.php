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
 * Version: 0.0.1
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

add_filter( 'woocommerce_payment_gateways', 'add_wc_babinr_gateway_class' );

add_action( 'plugins_loaded', 'init_babinr_wc_gateway_class' );

// Plugin load and Ask for Payment Class
function init_babinr_wc_gateway_class() {
    include_once( 'WC_Babinr_Gateway.php' );
}
