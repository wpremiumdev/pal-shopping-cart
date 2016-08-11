<?php

class PSC_Custom_PostType {

    public function create_product_detail_custom_posttype($post) {
        do_action('psc_simple_product_meta');
    }

    public function create_product_detail_custom_posttype_with_editor($post) {
        wp_nonce_field(plugin_basename(__FILE__), 'psc_noncename');
        $field_value = get_post_meta($post->ID, '_wp_editor_test_1') ? get_post_meta($post->ID, '_wp_editor_test_1') : '';
        $settings = array();
        if (is_array($field_value) && count($field_value) > 0) {
            wp_editor($field_value[0], '_wp_editor_test_1', $settings);
        } else {
            wp_editor($field_value, '_wp_editor_test_1', $settings);
        }
    }

    public function psc_order_detail_view($post) {
        do_action('psc_order_view_custom_meta', $post);
    }

    public function psc_order_item_view($post) {
        do_action('psc_order_item_view_custom_meta');
    }

    public function psc_order_notes($post) {
        do_action('psc_order_notes_custom_meta', $post);
    }

    public function paypal_shopping_cart_add_new_product_coupon($post) {
        do_action('psc_add_new_product_coupon');
    }

}

?>