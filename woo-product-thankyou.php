<?php
/**
 * Plugin Name: Woo Products on Thankyou Page
 * Version: 1.0.0
 * Description: Display WooCommerce Product on thank you page
 * Author: Muhammad Rehman
 * Author URI: http://muhammadrehman.com/
 * Plugin URI: http://itglobepk.com/
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'wp_enqueue_scripts', 'wpotp_style_script' );
function wpotp_style_script() {
    wp_enqueue_style( 'wpotp-style', plugins_url('css/style.css', __FILE__ ) );
}

include 'includes/class-settings_products_thankyou.php';
$product_thankyou = new WPOTP_Settings_Products_ThankYou();

include 'includes/class-display-product-thankyou.php';
$display_product_thankyou = new WPOTP_Display_Product_Thankyou();