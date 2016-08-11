<?php

if (!function_exists('psc_show_page_title')) {

    function psc_show_page_title($echo = true) {
        $PSC_Common_Function = new PSC_Common_Function();
        $shop_page_id = $PSC_Common_Function->psc_shop_page();
        $page_title = get_the_title($shop_page_id);
        $page_title = apply_filters('psc_page_title', $page_title);
        if ($echo)
            echo esc_html($page_title);
        else
            return esc_html($page_title);
    }

}

if (!function_exists('psc_result_count')) {

    function psc_result_count() {
        psc_get_template('loop/psc-result-count.php');
    }

}

if (!function_exists('psc_product_loop_start')) {

    function psc_product_loop_start($echo = true) {
        ob_start();
        psc_get_template('loop/psc-loop-start.php');
        if ($echo)
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

}

if (!function_exists('psc_product_subcategories')) {

    function psc_product_subcategories($args = array()) {
        global $wp_query;
        $defaults = array(
            'before' => '',
            'after' => '',
            'force_display' => false
        );
        $args = wp_parse_args($args, $defaults);
        extract($args);
        // Main query only
        if (!is_main_query() && !$force_display) {
            return;
        }
        // Don't show when filtering, searching or when on page > 1 and ensure we're on a product archive
        if (is_search() || is_filtered() || is_paged() || (!is_product_category() && !is_shop() )) {
            return;
        }
        // Check categories are enabled
        if (is_shop()) {
            return;
        }
        // Find the category + category parent, if applicable
        $term = get_queried_object();
        $parent_id = empty($term->term_id) ? 0 : $term->term_id;
        if (is_product_category()) {
            $display_type = get_psc_term_meta($term->term_id, 'display_type', true);

            switch ($display_type) {
                case 'products' :
                    return;
                    break;
                case '' :
                    if (get_option('psc_category_archive_display') == '') {
                        return;
                    }
                    break;
            }
        }
        // NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( http://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
        $product_categories = get_categories(apply_filters('psc_product_subcategories_args', array(
            'parent' => $parent_id,
            'menu_order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'taxonomy' => 'product_cat',
            'pad_counts' => 1
        )));
        if (!apply_filters('psc_product_subcategories_hide_empty', false)) {
            $product_categories = wp_list_filter($product_categories, array('count' => 0), 'NOT');
        }
        if ($product_categories) {
            echo $before;

            foreach ($product_categories as $category) {
                psc_get_template('content-product_cat.php', array(
                    'category' => $category
                ));
            }

            // If we are hiding products disable the loop and pagination
            if (is_product_category()) {
                $display_type = get_psc_term_meta($term->term_id, 'display_type', true);
                switch ($display_type) {
                    case 'subcategories' :
                        $wp_query->post_count = 0;
                        $wp_query->max_num_pages = 0;
                        break;
                    case '' :
                        if (get_option('psc_category_archive_display') == 'subcategories') {
                            $wp_query->post_count = 0;
                            $wp_query->max_num_pages = 0;
                        }
                        break;
                }
            }
            if (is_shop() && get_option('psc_shop_page_display') == 'subcategories') {
                $wp_query->post_count = 0;
                $wp_query->max_num_pages = 0;
            }
            echo $after;
            return true;
        }
    }

}

if (!function_exists('get_psc_term_meta')) {

    function get_psc_term_meta($term_id, $key, $single = true) {
        return get_metadata('psc_term', $term_id, $key, $single);
    }

}

if (!function_exists('psc_product_loop_end')) {

    function psc_product_loop_end($echo = true) {
        ob_start();
        psc_get_template('loop/psc-loop-end.php');
        if ($echo)
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

}

if (!function_exists('is_filtered')) {

    function is_filtered() {
        global $_chosen_attributes;
        $max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;
        $min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;       
        return apply_filters('psc_is_filtered', ( sizeof($_chosen_attributes) > 0 || isset($max_price) || isset($min_price)));
    }

}

if (!function_exists('is_product_category')) {

    function is_product_category($term = '') {
        return is_tax('product_cat', $term);
    }

}

if (!function_exists('is_shop')) {

    function is_shop() {
        $PSC_ABS_Product_obj = new PSC_Common_Function();
        return ( is_post_type_archive('psc_product') || is_page($PSC_ABS_Product_obj->psc_shop_page()) ) ? true : false;
    }

}

if (!function_exists('psc_pagination')) {

    function psc_pagination() {
        psc_get_template('loop/psc-pagination.php');
    }

}

if (!function_exists('psc_product_contant_loop_start')) {

    function psc_product_contant_loop_start() {
        psc_get_template('loop/psc-product-contant.php');
    }

}

if (!function_exists('psc_taxonomy_archive_description')) {

    function psc_taxonomy_archive_description() {
        if (is_tax(array('product_cat', 'product_tag')) && get_query_var('paged') == 0) {
            $description = psc_format_content(term_description());
            if ($description) {
                echo '<div class="term-description">' . $description . '</div>';
            }
        }
    }

}

if (!function_exists('psc_format_content')) {

    function psc_format_content($raw_string) {
        return apply_filters('psc_format_content', do_shortcode(shortcode_unautop(wpautop($raw_string))), $raw_string);
    }

}

if (!function_exists('psc_product_archive_description')) {

    function psc_product_archive_description() {
        if (is_post_type_archive('psc_product') && get_query_var('paged') == 0) {
            $PSC_ABS_Product_obj = new PSC_Common_Function();
            $shop_page = get_post($PSC_ABS_Product_obj->psc_shop_page());
            if ($shop_page) {
                $description = psc_format_content($shop_page->post_content);
                if ($description) {
                    echo '<div class="page-description">' . esc_html($description) . '</div>';
                }
            }
        }
    }

}

if (!function_exists('psc_product_cat_class')) {

    function psc_product_cat_class($class = '', $category = null) {
        echo 'class="' . esc_attr(join(' ', psc_get_product_cat_class($class, $category))) . '"';
    }

}

if (!function_exists('psc_get_product_cat_class')) {

    function psc_get_product_cat_class($class = '', $category = null) {
        global $psc_loop;
        $classes = is_array($class) ? $class : array_map('trim', explode(' ', $class));
        $classes[] = 'product-category';
        $classes[] = 'psc_product';
        if (( $psc_loop['loop'] - 1 ) % $psc_loop['columns'] == 0 || $psc_loop['columns'] == 1) {
            $classes[] = 'first';
        }
        if ($psc_loop['loop'] % $psc_loop['columns'] == 0) {
            $classes[] = 'last';
        }
        $classes = apply_filters('product_cat_class', $classes, $class, $category);
        return array_unique(array_filter($classes));
    }

}

if (!function_exists('psc_subcategory_thumbnail')) {

    function psc_subcategory_thumbnail($category) {
        $small_thumbnail_size = apply_filters('single_product_small_thumbnail_size', 'psc_shop_catalog');
        $dimensions = psc_get_image_size($small_thumbnail_size);
        $thumbnail_id = get_psc_term_meta($category->term_id, 'thumbnail_id', true);
        if ($thumbnail_id) {
            $image = wp_get_attachment_image_src($thumbnail_id, $small_thumbnail_size);
            $image = $image[0];
        } else {
            $image = placeholder_img_src();
        }
        if ($image) {
            $image = str_replace(' ', '%20', $image);
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($category->name) . '" width="' . esc_attr($dimensions['width']) . '" height="' . esc_attr($dimensions['height']) . '" />';
        }
    }

}

if (!function_exists('psc_template_loop_product_thumbnail')) {

    function psc_template_loop_product_thumbnail() {
        echo psc_get_product_thumbnail();
    }

}

if (!function_exists('psc_template_loop_product_title')) {

    function psc_template_loop_product_title() {
        psc_get_template('loop/title.php');
    }

}

if (!function_exists('psc_template_loop_price')) {

    function psc_template_loop_price() {
        psc_get_template('loop/price.php');
    }

}

if (!function_exists('psc_template_loop_add_to_cart')) {

    function psc_template_loop_add_to_cart($args = array()) {
        psc_get_template('loop/psc-add-to-cart.php', $args);
    }

}

if (!function_exists('psc_get_product_thumbnail')) {

    function psc_get_product_thumbnail($size = 'psc_shop_catalog', $deprecated1 = 0, $deprecated2 = 0) {
        global $post;
        if (has_post_thumbnail()) {
            return get_the_post_thumbnail($post->ID, $size);
        } elseif (placeholder_img_src()) {
            return placeholder_img($size);
        }
    }

}

if (!function_exists('placeholder_img_src')) {

    function placeholder_img_src() {
        return apply_filters('psc_placeholder_img_src', plugins_url('/admin/images/placeholder.png', dirname(__FILE__)));
    }

}

if (!function_exists('placeholder_img')) {

    function placeholder_img($size = 'psc_shop_thumbnail') {
        $dimensions = psc_get_image_size($size);
        $d_width = '';
        $d_height = '';
        if (is_array($dimensions['width']) && isset($dimensions['width']) && count($dimensions['width']) > 0) {
            $d_width = $dimensions['width'];
        }
        if (is_array($dimensions['height']) && isset($dimensions['height']) && count($dimensions['height']) > 0) {
            $d_height = $dimensions['height'];
        }
        return apply_filters('psc_placeholder_img', '<img src="' . placeholder_img_src() . '" alt="' . esc_attr('Placeholder') . '" width="' . esc_attr($d_width) . '" class="psc-placeholder wp-post-image" height="' . esc_attr($d_height) . '" />', $size, $dimensions);
    }

}

if (!function_exists('psc_get_image_size')) {

    function psc_get_image_size($image_size) {
        if (is_array($image_size)) {
            $width = isset($image_size[0]) ? $image_size[0] : '300';
            $height = isset($image_size[1]) ? $image_size[1] : '300';
            $crop = isset($image_size[2]) ? $image_size[2] : 1;
            $size = array(
                'width' => $width,
                'height' => $height,
                'crop' => $crop
            );
            $image_size = $width . '_' . $height;
        } elseif (in_array($image_size, array('psc_shop_thumbnail', 'psc_shop_catalog', 'psc_shop_single'))) {
            $size['width'] = get_option($image_size . '_image_size_width', array());
            $size['height'] = get_option($image_size . '_image_size_height', array());
            $size['crop'] = get_option($image_size . '_image_size', array());
            
           // $size           = get_option( $image_size . '_image_size', array() );
            $size['width'] = isset($size['width']) ? $size['width'] : '300';
            $size['height'] = isset($size['height']) ? $size['height'] : '300';
            
            if(isset($size['crop']))
            {
                $size['crop'] = ($size['crop'] == 'no') ? 0 : 1;
            }
            
            $size['crop'] = isset($size['crop']) ? $size['crop'] : 1;
        } else {
            $size = array(
                'width' => '300',
                'height' => '300',
                'crop' => 1
            );
        }
        return apply_filters('psc_get_image_size_' . $image_size, $size);
    }

}

if (!function_exists('psc_get_sidebar')) {

    function psc_get_sidebar() {
        psc_get_template('global/sidebar.php');
    }

}

if (!function_exists('psc_show_product_images')) {

    function psc_show_product_images() {
        psc_get_template('single-product/psc-product-image.php');
    }

}

if (!function_exists('psc_show_product_thumbnails')) {

    function psc_show_product_thumbnails() {
        psc_get_template('single-product/psc-product-thumbnails.php');
    }

}

if (!function_exists('psc_template_single_title')) {

    function psc_template_single_title() {
        psc_get_template('single-product/title.php');
    }

}

if (!function_exists('psc_template_single_price')) {

    function psc_template_single_price() {
        psc_get_template('single-product/price.php');
    }

}

if (!function_exists('psc_template_single_excerpt')) {

    function psc_template_single_excerpt() {
        psc_get_template('single-product/short-description.php');
    }

}

if (!function_exists('psc_template_single_meta')) {

    function psc_template_single_meta() {
        psc_get_template('single-product/meta.php');
    }

}

if (!function_exists('psc_template_single_stock')) {

    function psc_template_single_stock() {
        psc_get_template('single-product/psc-stock.php');
    }

}

if (!function_exists('psc_output_product_data_tabs')) {

    function psc_output_product_data_tabs() {
        psc_get_template('single-product/tabs.php');
    }

}

if (!function_exists('psc_simple_add_to_cart')) {

    function psc_simple_add_to_cart() {
        psc_get_template('single-product/simple.php');
    }

}

if (!function_exists('psc_cart_totals')) {

    function psc_cart_totals() {
        psc_get_template('cart/psc-cart-totals.php');
    }

}

if (!function_exists('psc_checkout_form_billing')) {

    function psc_checkout_form_billing() {
        psc_get_template('checkout/psc-form-billing.php');
    }

}

if (!function_exists('psc_checkout_before_order_review')) {

    function psc_checkout_before_order_review() {
        psc_get_template('checkout/psc-form-order-review.php');
    }

}

if (!function_exists('psc_simple_product_meta_content')) {

    function psc_simple_product_meta_content() {
        psc_get_template('content_meta/simple.php');
    }

}

if (!function_exists('psc_add_new_product_coupon')) {

    function psc_add_new_product_coupon() {
        psc_get_template('content_meta/coupons.php');
    }

}

if (!function_exists('psc_order_view_custom_meta')) {

    function psc_order_view_custom_meta($post) {
        psc_get_template('order/order-view.php');
    }

}

if (!function_exists('psc_order_item_view_custom_meta')) {

    function psc_order_item_view_custom_meta() {
        psc_get_template('order/order-item.php');
    }

}

if (!function_exists('psc_order_notes_custom_meta')) {

    function psc_order_notes_custom_meta($post) {
        psc_get_template('order/order-notes.php');
    }

}

if (!function_exists('psc_get_all_enable_payment_methods')) {

    function psc_get_all_enable_payment_methods() {
        psc_get_template('checkout/psc-payment-methods.php');
    }

}

if (!function_exists('psc_display_notice')) {

    function psc_display_notice() {
        global $PSC_ERROR_DISPLAY_NOTICE;
        $PSC_Common_Function = new PSC_Common_Function();
        $responce_array = $PSC_Common_Function->session_get('PSC_PAYMENT_ERROR');
        if (is_array($responce_array) && count($responce_array) > 0) {
            $PSC_ERROR_DISPLAY_NOTICE = $responce_array;
            psc_get_template('checkout/psc-cart-errors.php');
            $PSC_Common_Function->session_remove('PSC_PAYMENT_ERROR');
        }
    }

}

if (!function_exists('psc_display_notice_coupons')) {

    function psc_display_notice_coupons() {
        $PSC_Common_Function = new PSC_Common_Function();
        $responce_coupons = $PSC_Common_Function->session_get('coupon_cart_discount_msg');
        $responce_update_cart = $PSC_Common_Function->session_get('update_cart_message');
        if (( isset($responce_coupons) && !empty($responce_coupons) ) || ( isset($responce_update_cart) && !empty($responce_update_cart) )) {
            psc_get_template('cart/psc-coupon-msg.php');
        }
    }

}

if (!function_exists('psc_display_notice_empty_stock')) {

    function psc_display_notice_empty_stock() {
        $PSC_Common_Function = new PSC_Common_Function();
        $responce_stock = $PSC_Common_Function->session_get('add_to_cart_stock_is_empty');
        $item_stock = $PSC_Common_Function->session_get('add_to_cart_stock_is_big_item');
        if ((isset($responce_stock) && !empty($responce_stock)) || (isset($item_stock) && !empty($item_stock))) {
            psc_get_template('cart/psc-addtocart-msg.php');
        }
    }

}

if (!function_exists('enable_checkout_button_cart')) {

    function enable_checkout_button_cart($status, $name = "") {
        global $PSC_CHECKOUT_BUTTON_STATUS;
        global $PSC_SINGLE_PRODUCT_NAME;
        $PSC_CHECKOUT_BUTTON_STATUS = $status;
        $PSC_SINGLE_PRODUCT_NAME = $name;
        psc_get_template('payment_methods/cart_page_express_checkout.php');
    }

}
function own_post_thumbnail_html($html, $post, $post_thumbnail_id, $size, $attr) {     
        $post = get_post($post);
        if ('psc_product' == $post->post_type && ($size == 'psc_shop_single' || $size == 'psc_shop_catalog')) {
            return $html;
        } else if('psc_product' == $post->post_type && ($size != 'psc_shop_single' && $size != 'psc_shop_catalog' && $size != 'psc_shop_thumbnail')) {
            return '';
        } else {
            return $html;
        }        
    } 
 
    function own_post_thumbnail_attachment( $image, $attachment_id, $size, $icon) {  
        if( !is_admin() ) {
        $post_attachment = get_post($attachment_id);
        $post = get_post($post_attachment->post_parent);
        if ('psc_product' == $post->post_type && ($size == 'psc_shop_single' || $size == 'psc_shop_catalog')) {
            return $image;
        } else if ('psc_product' == $post->post_type && ($size != 'psc_shop_single' && $size != 'psc_shop_catalog' && $size != 'psc_shop_thumbnail')) {
            $image = false;
            return $image;
        } else {
            return $image;
        }    
        } else {
            return $image;
        }
    }