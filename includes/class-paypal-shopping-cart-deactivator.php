<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.premiumdev.com/
 * @since      1.0.0
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 * @author     wpgateways <wpgateways@gmail.com>
 */
class Paypal_Shopping_Cart_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        $add_to_cart_item = new PSC_Cart_Handler();
        $add_to_cart_item->destroy();
    }

}