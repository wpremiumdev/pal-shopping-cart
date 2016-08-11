<?php

/**
 * @link              https://www.premiumdev.com/
 * @since             1.0.0
 * @package           Paypal_Shopping_Cart
 *
 * @wordpress-plugin
 * Plugin Name:       PayPal Shopping Cart
 * Plugin URI:        https://www.premiumdev.com/
 * Description:       PayPal Shopping Cart is a powerful, extendable eCommerce plugin. Great for selling products online in your WordPress site.
 * Version:           1.2.3
 * Author:            wpgateways
 * Author URI:        https://www.premiumdev.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pal-shopping-cart
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('PAYPAL_FOR_PAYPAL_SHOPPING_CART_PLUGIN_DIR')) {
    define('PAYPAL_FOR_PAYPAL_SHOPPING_CART_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('PSC_FOR_WORDPRESS_LOG_DIR')) {
    define('PSC_FOR_WORDPRESS_LOG_DIR', plugin_dir_url(__FILE__));
}

if (!defined('PSC_PLUGIN_BASENAME')) {
    define('PSC_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if (!defined('PSC_FOR_WORDPRESS_SITE_URL')) {
    define('PSC_FOR_WORDPRESS_SITE_URL', get_site_url() . '/');
}

if (!defined('PSC_PLUGIN_DIR_PATH')) {
    define('PSC_PLUGIN_DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('PSC_PLUGIN_TAMPLATE_PATH')) {
    //define('PSC_PLUGIN_TAMPLATE_PATH', untrailingslashit( bloginfo('template_directory') ));
}

if (!defined('PSC_WORDPRESS_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('PSC_WORDPRESS_LOG_DIR', $upload_dir['basedir'] . '/pal-shopping-cart-logs/');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paypal-shopping-cart-activator.php
 */
function activate_paypal_shopping_cart() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-shopping-cart-activator.php';
    Paypal_Shopping_Cart_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paypal-shopping-cart-deactivator.php
 */
function deactivate_paypal_shopping_cart() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-shopping-cart-deactivator.php';
    Paypal_Shopping_Cart_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_paypal_shopping_cart');
register_deactivation_hook(__FILE__, 'deactivate_paypal_shopping_cart');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-paypal-shopping-cart.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_paypal_shopping_cart() {

    $plugin = new Paypal_Shopping_Cart();
    $plugin->run();
}

run_paypal_shopping_cart();