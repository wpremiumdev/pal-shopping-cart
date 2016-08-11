<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.premiumdev.com/
 * @since      1.0.0
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/admin/partials
 */
class PayPal_Shopping_Cart_Admin_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public function init_settings() {
        $setting_tabs = apply_filters('paypal_shopping_cart_options_setting_tab', array('psc_setting_general_tab' => 'General', 'psc_setting_product_tab' => 'Product', 'psc_setting_checkout_tab' => 'Checkout', 'psc_setting_emails_tab' => 'Emails'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'psc_setting_general_tab';
        $current_tab = sanitize_text_field($current_tab);
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . esc_url(admin_url('admin.php?page=psc_settings&tab=' . $name)) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
        </h2>
        <?php
        if ('psc_setting_checkout_tab' == $current_tab) {
            $paypal_shopping_cart_gateway_tabs = apply_filters('paypal_shopping_cart_gateways', array('paypal_express_checkout' => 'Express Checkout', 'paypal_pro' => 'PayPal Pro', 'paypal_pro_payflow' => 'PayPal Pro PayFlow', 'paypal_advanced' => 'PayPal Advanced'));
            ?>
            <ul class="subsubsub">
                <?php
                $gateway_current_tab = (isset($_GET['gateway'])) ? $_GET['gateway'] : 'paypal_express_checkout';
                foreach ($paypal_shopping_cart_gateway_tabs as $key => $value) {
                    $gateway_class = ($gateway_current_tab == $key) ? 'current' : '';
                    $seprater = ('paypal_advanced' == $key) ? '' : ' | ';
                    echo $usage_url = sprintf('<li><a class="%s" href="%s">%s</a> %s </li>', $gateway_class, admin_url(sprintf('admin.php?page=psc_settings&tab=%s&gateway=%s', esc_html($current_tab), esc_html($key))), esc_html($value), $seprater);
                }
                ?>
            </ul>
            <br class="clear">
            <?php
        }
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('paypal_shopping_cart_' . $setting_tabkey . '_setting_save_field');
                    do_action('paypal_shopping_cart_' . $setting_tabkey . '_setting');
                    break;
            }
        }
    }
}