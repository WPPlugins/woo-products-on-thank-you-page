<?php

/**
 * Created by PhpStorm.
 * User: Muhammad
 * Date: 12/25/2016
 * Time: 10:17 PM
 */
class WPOTP_Display_Product_Thankyou {

    function __construct() {
        add_action( 'woocommerce_thankyou', __CLASS__ . '::wpotp_display_products' );
        add_action('woocommerce_before_calculate_totals', __CLASS__ . '::wpotp_set_discount_price');
        add_filter( 'woocommerce_get_price_html', __CLASS__ . '::wpotp_discount_product_price',20,2 );
        add_filter( 'woocommerce_add_to_cart_redirect', __CLASS__ . '::wpotp_redirect_to_checkout' );
    }

    // Display Products on Thankyou Page
    function wpotp_display_products() {
        $enable = get_option('wc_product_thankyou_enable'); // Plugin Enable
        if($enable != 'yes') // If Setting Enable
            return;

        $token = rand(0,999) . rand(0,999) . rand(0,999); // Unique Token For Discounted Products
        update_user_meta(get_current_user_id(),'_discount_token',$token); // Update the Token in User

        $products = get_option('wc_product_thankyou_select_products'); // Get Settings

        // Create HTML
        $html = '<h3>'.get_option('wc_product_thankyou_title').'</h3>';
        $html .= '<p>'.get_option('wc_product_thankyou_description').'</p>';
        $html .= '<div class="woo-product-discount">';
        foreach($products as $product) {
            $_product = wc_get_product( $product );
            $html .= '<div class="woo-prod-item">';
                $html .= '<a href="' . get_permalink($product) . '?discount=1&prod_id='.$product.'&token='.$token.'"><div class="woo-prod-img">';
                if(get_the_post_thumbnail( $product, 'medium' ))
                    $html .= get_the_post_thumbnail( $product, 'medium' );
                else
                    $html .= '<img src="'. wc_placeholder_img_src() .'"/>';
                $html .= '</div>';
                $html .= '<b>'.get_the_title($product).'</b>';
                $discount_price = WPOTP_Display_Product_Thankyou::wpotp_calculate_discount_price($_product->get_price());
                if(!empty($discount_price))
                    $html .= ' <s> '.$_product->get_price_html().'</s> <b>' . get_woocommerce_currency_symbol() .$discount_price .'</b>';
                else
                    $html .= '<b> '.$_product->get_price_html().'</b>';
                $html .= '</a>';
                $html .= '<div class="add-cart-btn"><a href="'.get_permalink(get_page_by_path('checkout')).'?add-to-cart='.$product.'&discount=1&prod_id='.$product.'&token='.$token.'">Add to Card</a></div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        echo $html; // Print HTML
    }

    // Set Discount Price
    function wpotp_set_discount_price($cart_object) {
        $discount_request = intval($_GET['discount']);
        $prod_id = intval($_GET['prod_id']);
        $unique_token = esc_html($_GET['token']);
        if($discount_request == 1 && !empty($unique_token) && !empty($prod_id)) { // Request For Discount
            $valid = WPOTP_Display_Product_Thankyou::wpotp_check_valid_token(get_current_user_id());
            if(!$valid) // If Token is invalid
                return;

            foreach ( $cart_object->cart_contents as $key => $value ) {

                if($value['data']->id == $prod_id) {
                $discount_price = WPOTP_Display_Product_Thankyou::wpotp_calculate_discount_price($value['data']->price); // Get Discount Price
                $discount = $value['data']->price = $discount_price; // Apply Discount
                }
            }
        }
    }

    // Discount Price on Product Page
    function wpotp_discount_product_price($price, $product) {
        $discount_request = intval($_GET['discount']);
        $prod_id = intval($_GET['prod_id']);
        $unique_token = esc_html($_GET['token']);
        if($discount_request == 1 && !empty($unique_token) && !empty($prod_id)) { // Request For Discount
            $valid = WPOTP_Display_Product_Thankyou::wpotp_check_valid_token(get_current_user_id());
            if(!$valid) // If Token is invalid
                return $price;

            $discount_price = WPOTP_Display_Product_Thankyou::wpotp_calculate_discount_price($product->price);
            $price = '<s>' . $price . '</s> ' . get_woocommerce_currency_symbol() . $discount_price;
            return $price;
        } else {
            return $price;
        }
    }

    // Redirect To Checkout When Purchase throw Discount
    function wpotp_redirect_to_checkout() {
        $discount_request = intval($_GET['discount']);
        $prod_id = intval($_GET['prod_id']);
        $unique_token = esc_html($_GET['token']);
        if($discount_request == 1 && !empty($unique_token) && !empty($prod_id)) { // Request For Discount
            $valid = WPOTP_Display_Product_Thankyou::wpotp_check_valid_token(get_current_user_id());
            if(!$valid)
                return;

            return get_permalink(get_page_by_path('checkout')) . '?discount=1&prod_id='.$prod_id.'&token='.$unique_token;
        }
    }

    // Check Valid Token Request
    function wpotp_check_valid_token($user_id) {
        $user_token = get_user_meta($user_id,'_discount_token',true);
        $unique_token = esc_html($_GET['token']);
        if($user_token != $unique_token)
            return false;
        else
            return true;
    }

    // Calculate Discount Price
    function wpotp_calculate_discount_price( $price ) {
        if(get_option('wc_product_thankyou_discount_on_next_order') > 0) {
            $discount = $price * get_option('wc_product_thankyou_discount_on_next_order');
            $discount_price = $discount / 100;
            return $discount_price;
        }
    }
}