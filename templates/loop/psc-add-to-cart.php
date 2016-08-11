<?php

/**
 * Loop Add to Cart 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();
$available_price = $PSC_Common_Function->psc_add_to_cart_text($post);
if (isset($available_price) && 'Add to cart' == $available_price) {
    echo apply_filters('psc_loop_add_to_cart_link', sprintf('<a href="%s" id="%s" rel="nofollow" psc-product-id="%s" psc-product_sku="%s" psc-quantity="%s" class="psc-button %s product_type_%s"><span class="psc_text_add_to_cart%s">%s </span> </a><a href="%s" class="view_cart%s view_cart"" title="View Cart" hidden>View Cart</a><input type="hidden" id="add_cart_after_redirect_behaviour" value="%s">', esc_url($PSC_Common_Function->psc_add_to_cart_url($post)), //link to product
                    esc_attr($PSC_Common_Function->psc_add_to_cart_class($post) . $post->ID), esc_attr($post->ID), //product_id
                    esc_attr($PSC_Common_Function->psc_get_sku($post)), //sku
                    esc_attr(isset($quantity) ? $quantity : 1 ), //qty
                    esc_attr($PSC_Common_Function->psc_add_to_cart_class($post)), //class add stock
                    esc_attr($PSC_Common_Function->psc_get_product_type($post)), //product type simple
                    esc_attr($post->ID), esc_html($PSC_Common_Function->psc_add_to_cart_text($post)), //button Taxt                   
                    esc_html(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())), 
                    esc_attr($post->ID), 
                    esc_html(($PSC_Common_Function->add_cart_after_redirect_behaviour())?get_permalink($PSC_Common_Function->add_cart_after_redirect_behaviour()):'')
            ), $post);
} else {
    echo apply_filters('psc_loop_add_to_cart_link', sprintf('<a href="' . esc_url($PSC_Common_Function->psc_readmore_text($post)) . '" rel="nofollow1" class="psc-button">' . esc_html($PSC_Common_Function->psc_add_to_cart_text($post)) . '</a>'), $post);
}