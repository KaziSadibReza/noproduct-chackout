<?php
/**
 * Order Details Shortcode Functions
 *
 * @package Kadence Child
 * @version 1.0.0
 */

defined('ABSPATH') || exit('Direct access not allowed');

/**
 * Shortcode to display order details
 * 
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function order_details_display_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'limit' => 10, // Number of orders to display
        'user_orders_only' => 'yes', // Show only current user's orders by default
    ), $atts, 'order_details');

    $limit = intval($atts['limit']);
    $user_orders_only = ($atts['user_orders_only'] === 'yes');
    
    // Buffer output
    ob_start();
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        $message = __('WooCommerce is required for this shortcode to work.', 'kadence-child');
        include get_stylesheet_directory() . '/templates/order-details-template.php';
        return ob_get_clean();
    }
    
    // Get current user if showing only user orders
    $user_id = 0;
    if ($user_orders_only) {
        if (!is_user_logged_in()) {
            $message = __('Please log in to view your orders.', 'kadence-child');
            include get_stylesheet_directory() . '/templates/order-details-template.php';
            return ob_get_clean();
        }
        $user_id = get_current_user_id();
    }
    
    // Set up the query arguments
    $query_args = array(
        'limit' => $limit,
        'return' => 'objects',
    );
    
    // Add user ID if showing only user orders
    if ($user_id > 0) {
        $query_args['customer_id'] = $user_id;
    }
    
    // Get orders
    $orders = wc_get_orders($query_args);
    
    // Include the template
    include get_stylesheet_directory() . '/templates/order-details-template.php';
    
    return ob_get_clean();
}
add_shortcode('order_details', 'order_details_display_shortcode');

/**
 * Enqueue styles for the order details table
 */
function order_details_table_styles() {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'order_details')) {
        wp_enqueue_style(
            'order-details-table-style',
            get_stylesheet_directory_uri() . '/assets/css/order-details-table.css',
            array(),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'order_details_table_styles');