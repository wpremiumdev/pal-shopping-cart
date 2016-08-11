<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.premiumdev.com/
 * @since      1.0.0
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 * @author     wpgateways <wpgateways@gmail.com>
 */
class Paypal_Shopping_Cart_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                'pal-shopping-cart', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}