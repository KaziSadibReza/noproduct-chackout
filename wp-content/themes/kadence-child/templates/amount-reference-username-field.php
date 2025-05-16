<?php
defined('ABSPATH') || exit('What are doing you silly human');
/**
 * Checkout Amount, Reference Number, and Username Fields
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */
?>
<div class="custom-checkout-fields">
    <h3><?php _e('Montant total', 'woocommerce'); ?></h3>
    <p class="form-row form-row-wide">
        <input type="number" class="input-text" name="custom_amount" id="custom_amount"
            value="<?php echo esc_attr(WC()->session->get('custom_amount', '')); ?>"
            placeholder="<?php esc_attr_e('Montant', 'woocommerce'); ?>" min="1" step="any" required
            aria-label="<?php esc_attr_e('Montant total', 'woocommerce'); ?>" />
    </p>

    <p class="form-row form-row-wide">
        <input type="text" class="input-text" name="reference_number" id="reference_number" value=""
            placeholder="<?php esc_attr_e('Référence de la commande', 'woocommerce'); ?>" required
            aria-label="<?php esc_attr_e('Référence de la commande', 'woocommerce'); ?>" />
    </p>
    <p class="form-row form-row-wide">
        <input type="text" class="input-text" name="custom_username" id="custom_username" value=""
            placeholder="<?php esc_attr_e('Pseudo TikTok', 'your-theme-text-domain'); ?>" required
            aria-label="<?php esc_attr_e('Pseudo TikTok', 'your-theme-text-domain'); ?>" />
    </p>

</div>