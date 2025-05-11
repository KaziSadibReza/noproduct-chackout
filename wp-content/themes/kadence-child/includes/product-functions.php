<?php
defined('ABSPATH') || exit('What are doing you silly human');
/**
 * Product all functions
 *
 * @package WooCommerce\Templates
 * @version 9.4.0
 */


 /**
  * @since 1.0.0
  * add a 0 USD product to the cart if not already present
  */
add_action('woocommerce_before_checkout_form', 'add_zero_usd_product_to_cart');

function add_zero_usd_product_to_cart() {
    $product_id = 64; // Replace with your 0 USD product ID
    $found = false;

    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        WC()->cart->add_to_cart($product_id);
    }
}

/**
 * @since 1.0.0
 * Update the cart price based on the custom amount entered by the user
 */
add_action('woocommerce_before_calculate_totals', 'update_cart_price_from_custom_amount', 20, 1);

function update_cart_price_from_custom_amount($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    // Get custom amount from session or POST
    $custom_price = WC()->session ? WC()->session->get('custom_amount') : 0;
    if (isset($_POST['custom_amount'])) {
        $custom_price = floatval($_POST['custom_amount']);
    }
    
    if ($custom_price > 0) {
        $target_product_id = 64; // Your 0 USD product ID

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $target_product_id) {
                $cart_item['data']->set_price($custom_price);
            }
        }
    }
}

/**
 * @since 1.0.0
 * Handle AJAX request to update the cart amount
 */
add_action('wp_ajax_update_cart_amount', 'handle_update_cart_amount');
add_action('wp_ajax_nopriv_update_cart_amount', 'handle_update_cart_amount');

function handle_update_cart_amount() {
    check_ajax_referer('update_cart_amount', 'nonce');

    if (!isset($_POST['custom_amount'])) {
        wp_send_json_error('No amount provided');
        return;
    }

    $custom_price = floatval($_POST['custom_amount']);
    $target_product_id = 64; // Your 0 USD product ID

    // Store the custom amount in the session
    WC()->session->set('custom_amount', $custom_price);

    // Force WooCommerce to recalculate totals
    WC()->cart->calculate_totals();
    
    // Get the new total
    $total = WC()->cart->get_total();
    
    wp_send_json_success(array(
        'total' => $total,
        'price' => wc_price($custom_price)
    ));
}