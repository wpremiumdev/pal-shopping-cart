<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
global $PSC_CHECKOUT_BUTTON_STATUS;
global $PSC_SINGLE_PRODUCT_NAME;
$direct_payment_page = ($PSC_CHECKOUT_BUTTON_STATUS) ? $PSC_CHECKOUT_BUTTON_STATUS : '';
$single_product_name = ($PSC_SINGLE_PRODUCT_NAME) ? $PSC_SINGLE_PRODUCT_NAME : '';
$PSC_CHECKOUT_BUTTON_STATUS = "";
$PSC_Common_Function = new PSC_Common_Function();
if ($direct_payment_page == 'single') {
    //echo apply_filters('psc_enable_checout_button', sprintf('<a href="' . esc_url(add_query_arg(array("psc-api" => "Paypal_Shopping_Cart_Express_Checkout", "page" => "$direct_payment_page", "product_name" => "$single_product_name", "psc_action" => "PayPal_Express_Checkout_Method", "psc_payment_method" => 'PayPal_Express_Checkout_Method'), home_url())) . '" class="direct_payment_to_' . esc_attr($direct_payment_page) . '_product"><img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png" alt="Check out with PayPal" /></a>'));    
    echo apply_filters('psc_enable_checout_button',sprintf('<a href="%s" class="direct_payment_to_%s_product"><img src="%s" alt="Check out with PayPal" /></a>',
            add_query_arg(array("psc-api" => "Paypal_Shopping_Cart_Express_Checkout", "page" => "$direct_payment_page", "product_name" => "$single_product_name", "psc_action" => "PayPal_Express_Checkout_Method", "psc_payment_method" => 'PayPal_Express_Checkout_Method'), home_url()),
            esc_attr($direct_payment_page),
            'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png'));
} else {
   // echo apply_filters('psc_enable_checout_button', sprintf('<a href="' . esc_url(add_query_arg(array("psc-api" => "Paypal_Shopping_Cart_Express_Checkout", "page" => "$direct_payment_page", "psc_action" => "PayPal_Express_Checkout_Method", "psc_payment_method" => 'PayPal_Express_Checkout_Method'), home_url())) . '" class="direct_payment_to_' . esc_attr($direct_payment_page) . '_product"><img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png" alt="Check out with PayPal" /></a>'));
    echo apply_filters('psc_enable_checout_button',sprintf('<a href="%s" class="direct_payment_to_%s_product"><img src="%s" alt="Check out with PayPal" /></a>',
            add_query_arg(array("psc-api" => "Paypal_Shopping_Cart_Express_Checkout", "page" => "$direct_payment_page", "psc_action" => "PayPal_Express_Checkout_Method", "psc_payment_method" => 'PayPal_Express_Checkout_Method'), home_url()),
            esc_attr($direct_payment_page),
            'https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png'));
}
?>