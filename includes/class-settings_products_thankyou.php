<?php

/**
 * Created by PhpStorm.
 * User: Muhammad
 * Date: 12/25/2016
 * Time: 1:36 PM
 */
class WPOTP_Settings_Products_ThankYou {

    function __construct() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::wpotp_add_product_thankyou_setting_tab', 50 );
        add_action( 'woocommerce_settings_tabs_products_thankyou', __CLASS__ . '::wpotp_product_thankyou_setting_tab' );
        add_action( 'woocommerce_update_options_products_thankyou', __CLASS__ . '::wpotp_update_product_thankyou_setting' );
    }

    public static function wpotp_add_product_thankyou_setting_tab( $settings_tabs ) {
        $settings_tabs['products_thankyou'] = __( 'Products on ThankYou', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }

    public function wpotp_product_thankyou_setting_tab() {
        woocommerce_admin_fields( WPOTP_Settings_Products_ThankYou::wpotp_get_product_thankyou_settings() );
    }

    public function wpotp_get_product_thankyou_settings() {

        // Product IDs
        $args = array(
            'posts_per_page' => -1,
            'post_type'   => 'product'
        );

        $get_post_ids = get_posts( $args );
        $products_ids = array();
        $product_taxonomies = array();
        foreach($get_post_ids as $get_post_id) {
            $products_ids[$get_post_id->ID] = $get_post_id->post_title;
        }

        // Product Categories
        $terms = get_terms( array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ) );

        foreach($terms as $term) {
            $product_taxonomies[$term->term_id] = $term->name;
        }

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Products on Thank You Page', 'woocommerce-settings-tab-demo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_product_thankyou_section_title'
            ),
            'enable_product_thankyou' => array(
                'name'     => __( 'Enable', 'woocommerce-settings-tab-demo' ),
                'type'     => 'checkbox',
                'desc'    => __( '<i>Enable to display products on thankyou page</i>', 'woocommerce-settings-tab-demo' ),
                'id'       => 'wc_product_thankyou_enable'
            ),
            'tittle' => array(
                'name' => __( 'Title', 'woocommerce-settings-tab-demo' ),
                'type' => 'text',
                'desc'    => __( '<i>Text to display on thankyou page before the products</i>', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_product_thankyou_title'
            ),
            'description' => array(
                'name' => __( 'Description', 'woocommerce-settings-tab-demo' ),
                'type' => 'textarea',
                'desc'    => __( '<i>Add Description on thankyoy pag products</i>', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_product_thankyou_description'
            ),
            'select_products' => array(
                'name' => __( 'Products to Show', 'woocommerce-settings-tab-demo' ),
                'type' => 'multiselect',
                'desc'    => __( '<i>Select Product to show on Thankyou page</i>', 'woocommerce-settings-tab-demo' ),
                'options' => $products_ids,
                'id'   => 'wc_product_thankyou_select_products'
            ),
            /*'dispaly_product_category' => array(
                'name'     => __( 'Add Product By Category', 'woocommerce-settings-tab-demo' ),
                'type'     => 'checkbox',
                'desc'    => __( '<i>Display Catergory instead of products</i>', 'woocommerce-settings-tab-demo' ),
                'id'       => 'wc_product_thankyou_dispaly_product_category'
            ),*/
            /*'select_category' => array(
                'name' => __( 'Products to Show', 'woocommerce-settings-tab-demo' ),
                'type' => 'multiselect',
                'desc'    => __( '<i>Select Category Products to show on Thankyou page</i>', 'woocommerce-settings-tab-demo' ),
                'options' => $product_taxonomies,
                'id'   => 'wc_product_thankyou_select_category'
            ),*/
            'discount_on_next_order' => array(
                'name' => __( 'Discount %', 'woocommerce-settings-tab-demo' ),
                'type' => 'text',
                'default' => 50,
                'desc'    => __( '<i>Discount on next order</i>', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_product_thankyou_discount_on_next_order'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_demo_section_end'
            )
        );
        return apply_filters( 'wc_settings_tab_demo_settings', $settings );
    }

    function wpotp_update_product_thankyou_setting() {
        woocommerce_update_options( WPOTP_Settings_Products_ThankYou::wpotp_get_product_thankyou_settings() );
    }
}