<?php

if (!defined('ABSPATH')) {
    exit;
}

$PSC_Common_Function = new PSC_Common_Function();
$result_array = $PSC_Common_Function->is_enable_payment_methods();

if( $PSC_Common_Function->get_cart_total_is_empty()  ){ 
    if (is_array($result_array) && count($result_array) > 0) {
        echo '<ul class="psc_payment_methods">';
        $count = 0;
        foreach ($result_array as $key => $value) {
            $is_checked ="";
            if( $count == 0 ){
                $is_checked = 'checked = "checked"';
            }
            $count = $count + 1;
            echo sprintf('<li class="psc_payment_method_%s">'
                    . '<input id="psc_payment_method_%s" type="radio" class="input-radio-button" name="psc_payment_method" value="%s" %s data-order_button_text="">'
                    . '<label for="psc_payment_method_%s"><span style="float: left;margin-left: -1.5em;">%s</span>'
                    . '<img style="margin-left: -2em;height: 25px;" src="%s"></label><div class="psc_payment_box psc_payment_method_%s"><p>%s</p></div></li>',esc_html(str_replace(' ', '_', $value['name'])),esc_html(str_replace(' ', '_', $value['name'])),esc_html(str_replace(' ', '_', $value['method'])),$is_checked,esc_html(str_replace(' ', '_', $value['name'])),esc_html($value['name']),esc_url($value['icon']),esc_html(str_replace(' ', '_', $value['name'])),$value['discription'],'"');
        }
        echo '</ul>';
        echo '<div class="psc-row psc-place-order">' . apply_filters('psc_checkout_proceed_to_paypal', sprintf('<input type="submit" class="psc-button" name="submit" id="psc_place_order" value="%s" data-value="">', esc_html((get_option('psc_checkout_button_general_settings')) ? get_option('psc_checkout_button_general_settings') : 'Proceed to PayPal'))) . '</div>';
    } else {
        echo '<ul class="psc_payment_methods"><li class=""><label><p><strong>' . apply_filters('psc_no_enable_payment_methods', __("Sorry, it seems that there are no payment methods enabled.", "pal-shopping-cart")) . '</strong></p></label></li></ul>';
        }
} else {
    echo '<div class="psc-return-shop-page"><p>Your cart is currently empty.</p><a  class="psc-button" href="' . esc_url(get_permalink($psc_shop_page_id)) . '">' . __("Return to Shop Page", "pal-shopping-cart") . '</a></div>';
}