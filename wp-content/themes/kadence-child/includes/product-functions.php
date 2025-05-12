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

/**
 * Save custom checkout fields to order
 */
add_action('woocommerce_checkout_create_order', 'save_custom_checkout_fields', 10, 2);
function save_custom_checkout_fields($order, $data) {
    if (isset($_POST['reference_number'])) {
        $order->update_meta_data('_reference_number', sanitize_text_field($_POST['reference_number']));
    }
    if (isset($_POST['custom_username'])) {
        $order->update_meta_data('_custom_username', sanitize_text_field($_POST['custom_username']));
    }
}

/**
 * Display custom fields in order admin panel
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_fields_in_admin', 10, 1);
function display_custom_fields_in_admin($order) {
    echo '<div class="order-custom-fields">';
    echo '<h3>' . __('TikTok Order Details', 'woocommerce') . '</h3>';
    
    $reference_number = $order->get_meta('_reference_number');
    if ($reference_number) {
        echo '<p><strong>' . __('Reference Number:', 'woocommerce') . '</strong> ' . esc_html($reference_number) . '</p>';
    }
    
    $custom_username = $order->get_meta('_custom_username');
    if ($custom_username) {
        echo '<p><strong>' . __('TikTok Username:', 'woocommerce') . '</strong> ' . esc_html($custom_username) . '</p>';
    }
    echo '</div>';
}

/**
 * Add custom fields to order emails
 */
add_action('woocommerce_email_order_details', 'add_custom_fields_to_emails', 20, 4);
function add_custom_fields_to_emails($order, $sent_to_admin, $plain_text, $email) {    
    if ($plain_text) {
        // Plain text email
        $reference_number = $order->get_meta('_reference_number');
        $custom_username = $order->get_meta('_custom_username');
        
        echo "\n==========\n\n";
        echo "TikTok Order Details\n\n";
        if ($reference_number) {
            echo "Reference Number: " . $reference_number . "\n";
        }
        if ($custom_username) {
            echo "TikTok Username: " . $custom_username . "\n";
        }
        echo "\n==========\n\n";    
    } else {        // HTML email
        echo '<div style="margin-bottom: 40px;">';
        echo '<h2>' . __('TikTok Order Details', 'woocommerce') . '</h2>';
        echo '<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin-bottom: 40px;">';
        
        $reference_number = $order->get_meta('_reference_number');
        if ($reference_number) {
            echo '<tr>';
            echo '<th class="td" scope="row" style="text-align:left;">' . __('Reference Number:', 'woocommerce') . '</th>';
            echo '<td class="td" style="text-align:left;">' . esc_html($reference_number) . '</td>';
            echo '</tr>';
        }
        
        $custom_username = $order->get_meta('_custom_username');
        if ($custom_username) {
            echo '<tr>';
            echo '<th class="td" scope="row" style="text-align:left;">' . __('TikTok Username:', 'woocommerce') . '</th>';
            echo '<td class="td" style="text-align:left;">' . esc_html($custom_username) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
    }
}

/**
 * Add custom fields to customer order view
 */
add_action('woocommerce_order_details_after_order_table', 'display_custom_fields_in_order_view', 10, 1);
function display_custom_fields_in_order_view($order) {
    echo '<section class="custom-order-details">';
    echo '<h2>' . __('TikTok Order Details', 'woocommerce') . '</h2>';
    echo '<table class="woocommerce-table shop_table custom_details">';
    
    $reference_number = $order->get_meta('_reference_number');
    if ($reference_number) {
        echo '<tr>';
        echo '<th>' . __('Reference Number:', 'woocommerce') . '</th>';
        echo '<td>' . esc_html($reference_number) . '</td>';
        echo '</tr>';
    }
    
    $custom_username = $order->get_meta('_custom_username');
    if ($custom_username) {
        echo '<tr>';
        echo '<th>' . __('TikTok Username:', 'woocommerce') . '</th>';
        echo '<td>' . esc_html($custom_username) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</section>';
}