<?php
/**
 * Template for displaying order details
 *
 * @package Kadence Child
 * @version 1.0.0
 */

defined('ABSPATH') || exit('Direct access not allowed');

/**
 * @var array $orders WooCommerce orders
 * @var string $message Error or info message to display
 */
?>

<?php if (isset($message) && !empty($message)) : ?>
    <p><?php echo wp_kses_post($message); ?></p>
<?php elseif (empty($orders)) : ?>
    <p><?php _e('No orders found.', 'kadence-child'); ?></p>
<?php else : ?>
    <table class="order-details-table">
        <thead>
            <tr>
                <th><?php _e('Reference', 'kadence-child'); ?></th>
                <th><?php _e('Date', 'kadence-child'); ?></th>
                <th><?php _e('Total', 'kadence-child'); ?></th>
                <th><?php _e('Carrier', 'kadence-child'); ?></th>
                <th><?php _e('Status', 'kadence-child'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order) : 
                // Get custom reference number if exists
                $reference = $order->get_meta('reference_number') ?: $order->get_order_number();
                
                // Get shipping method as carrier
                $shipping_methods = $order->get_shipping_methods();
                $carrier = '';
                if (!empty($shipping_methods)) {
                    $shipping_method = reset($shipping_methods);
                    $carrier = $shipping_method->get_method_title();
                }
            ?>
                <tr>
                    <td><?php echo esc_html($reference); ?></td>
                    <td><?php echo esc_html($order->get_date_created()->date_i18n(get_option('date_format') . ' ' . get_option('time_format'))); ?></td>
                    <td><?php echo wp_kses_post($order->get_formatted_order_total()); ?></td>
                    <td><?php echo esc_html($carrier); ?></td>
                    <td><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
