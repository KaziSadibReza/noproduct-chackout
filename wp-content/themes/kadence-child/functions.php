<?php
/**
 * Kadence Child Theme functions and definitions
 */

function kadence_child_enqueue_styles() {
    wp_enqueue_style( 'kadence-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'kadence-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'kadence-style' ),
        wp_get_theme()->get('Version')
    );
}

function kadence_child_enqueue_scripts() {
    if (is_checkout()) {
        wp_enqueue_script(
            'cart-update',
            get_stylesheet_directory_uri() . '/assets/js/cart-update.js',
            array('jquery', 'woocommerce'),
            wp_get_theme()->get('Version'),
            true
        );
        
        wp_localize_script('cart-update', 'cartUpdateParams', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('update_cart_amount')
        ));
    }
}
add_action('wp_enqueue_scripts', 'kadence_child_enqueue_scripts');
add_action( 'wp_enqueue_scripts', 'kadence_child_enqueue_styles' );

/**
 * @since 1.0.0
 * Remove redirect to cart page when adding a product to the cart
 * 
 */
add_filter('woocommerce_checkout_redirect_empty_cart', '__return_false');
add_filter('woocommerce_checkout_update_order_review_expired', '__return_false');

/**
 * @since 1.0.0
 * Remove the coupon form from the checkout page
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

/**
 * @since 1.0.0
 * Product all functions
 */
include_once( 'includes/product-functions.php' );

/**
 * @since 1.0.0
 * Add fixed delivery fee to the cart and checkout
 */
add_action( 'woocommerce_cart_calculate_fees', 'add_fixed_delivery_fee' );
function add_fixed_delivery_fee( $cart ) {
    if ( is_admin() && ! defined('DOING_AJAX') ) return;
    $fee_name   = 'Delivery Fee';
    $fee_amount = 5; // $5 fee
    $cart->add_fee( $fee_name, $fee_amount );
}

// Add a custom Amount field before the customer details on the checkout page
add_action( 'woocommerce_checkout_before_customer_details', 'custom_add_amount_field', 10 );

function custom_add_amount_field() {
    include get_stylesheet_directory() . '/templates/amount-reference-username-field.php';
}