<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.premiumdev.com/
 * @since      1.0.0
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/public
 * @author     wpgateways <wpgateways@gmail.com>
 */
class Paypal_Shopping_Cart_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->psc_add_image_sizes();
        add_filter('wp_nav_menu_items', 'do_shortcode');
        add_shortcode('psc_shop', array($this, 'psc_load_shop_page'));
        add_shortcode('psc_cart', array($this, 'psc_load_cart_page'));
        add_shortcode('psc_checkout', array($this, 'psc_load_checkout_page'));
        add_shortcode('psc_review_order', array($this, 'psc_load_pscrevieworder_page'));
        add_shortcode('psc_order_received', array($this, 'psc_load_pscordercomplete_page'));
        add_action('get_footer', array($this, 'psc_get_sidebar_by_shortcode'), 1);
        add_filter('body_class', array($this, 'psc_remove_body_singular_classes'), 99);
        add_filter('the_content', array($this, 'psc_add_content_shortcode'), 9999999);
        add_shortcode('display_product', array($this, 'psc_display_product_view'), 99);
        add_action('wp', array($this, 'psc_detect_shortcode'), 10);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/paypal-shopping-cart-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'customselect_css', PSC_FOR_WORDPRESS_LOG_DIR . 'admin/css/jquery-customselect.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'colorbox_css', plugin_dir_url(__FILE__) . 'css/colorbox.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/paypal-shopping-cart-public.js', array('jquery'), $this->version, false);
        if (wp_script_is($this->plugin_name)) {
            wp_localize_script($this->plugin_name, 'paypal_shopping_cart_url_params', apply_filters('paypal_shopping_cart_url_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'paypal_shopping_cart_url' => wp_create_nonce("paypal_shopping_cart_url"),
            )));
        }
        wp_enqueue_script($this->plugin_name . 'blockui', plugin_dir_url(__FILE__) . 'js/paypal-shopping-cart-public-blockUI.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'customselect_js', PSC_FOR_WORDPRESS_LOG_DIR . 'admin/js/jquery-customselect.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'colorbox_js', plugin_dir_url(__FILE__) . 'js/jquery.colorbox.js', array('jquery'), $this->version, false);

        wp_localize_script($this->plugin_name, 'base_plugin_url', PSC_FOR_WORDPRESS_LOG_DIR);
        wp_localize_script($this->plugin_name, 'cart_page', $this->psc_cart_page() );
    }

    public function psc_cart_page() {
        $result = '';
        $result = get_option('psc_cartpage_product_settings');
        if (empty($result)) {
            $cartpage = get_page_by_title('Cart');
            $result = $cartpage->ID;
        }
        return get_permalink($result);
    }

    public function psc_add_image_sizes() {
        $shop_thumbnail = psc_get_image_size('psc_shop_thumbnail');
        $shop_catalog = psc_get_image_size('psc_shop_catalog');
        $shop_single = psc_get_image_size('psc_shop_single');

        add_image_size('psc_shop_thumbnail', $shop_thumbnail['width'], $shop_thumbnail['height'], $shop_thumbnail['crop']);
        add_image_size('psc_shop_catalog', $shop_catalog['width'], $shop_catalog['height'], $shop_catalog['crop']);
        add_image_size('psc_shop_single', $shop_single['width'], $shop_single['height'], $shop_single['crop']);
    }

    public function paypal_shopping_cart_get_shoppage($template) {
        global $post;
        $PSC_Common_Function = new PSC_Common_Function();

        $psc_shoppage = (get_option('psc_shoppage_product_settings')) ? get_option('psc_shoppage_product_settings') : '';
        $psc_cartpage = (get_option('psc_cartpage_product_settings')) ? get_option('psc_cartpage_product_settings') : '';
        $psc_checkoutpage = (get_option('psc_checkoutpage_product_settings')) ? get_option('psc_checkoutpage_product_settings') : '';

        if (empty($psc_shoppage)) {
            $psc_shoppage = $PSC_Common_Function->psc_shop_page();
        }
        if (empty($psc_cartpage)) {
            $psc_cartpage = $PSC_Common_Function->psc_cart_page();
        }
        if (empty($psc_checkoutpage)) {
            $psc_checkoutpage = $PSC_Common_Function->psc_checkout_page();
        }

        if (isset($post) && !empty($post)) {
            $file = '';
            if (is_single() && get_post_type() == 'psc_product') {
                $file = 'single-psc_product.php';
                $find[] = $file;
                $find[] = $this->template_path() . $file;
            }
            if ($file) {
                $template = locate_template(array_unique($find));
                if (!$template) {
                    $template = PSC_PLUGIN_DIR_PATH . '/templates/' . $file;
                }
            }
        }
        return $template;
    }

    public function template_path() {
        return apply_filters('psc_template_path', 'pal-shopping-cart/');
    }

    public function psc_load_shop_page() {
        try {
            psc_get_template('product_listing.php');
        } catch (Exception $ex) {
            
        }
    }

    public function psc_load_cart_page() {
        try {
            psc_get_template('cart/psc-cart.php');
        } catch (Exception $ex) {
            
        }
    }

    public function psc_load_checkout_page() {
        try {

            if ((isset($_GET['order-pay']) && !empty($_GET['order-pay'])) && (isset($_GET['psc-order-pay']) && 'Receipt' == $_GET['psc-order-pay'])) {
                psc_get_template('advance_gateways/paypal-advance-shortcode-checkout.php');
            } else {
            psc_get_template('checkout/psc-form-checkout.php');
            }
        } catch (Exception $ex) {
            
        }
    }

    public function psc_load_pscrevieworder_page() {
        try {
            psc_get_template('order/psc-order-details.php');
        } catch (Exception $ex) {
            
        }
    }

    public function psc_load_pscordercomplete_page() {
        try {

            if (isset($_GET['status']) && 'completed' == $_GET['status']) {
                if (!class_exists('Paypal_Shopping_Cart_PayPal_Advanced')) {
                    require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-advanced.php';
                }
                $Paypal_Shopping_Cart_PayPal_Advanced = new Paypal_Shopping_Cart_PayPal_Advanced();
                $Paypal_Shopping_Cart_PayPal_Advanced->pal_shopping_cart_paypal_advanced_relay_response();
            } else {
            psc_get_template('order/psc-order-complete.php');
            }
            
        } catch (Exception $ex) {
            
        }
    }

    public function psc_get_sidebar_by_shortcode() {
        global $post;
        if(!isset($post->ID)){
            return false;
        }
        
        $enable_sidebar = get_option('psc_enable_sidebar_general_settings')?get_option('psc_enable_sidebar_general_settings'):'no';
        
        if($enable_sidebar == 'no'){
            return false;
        }
        
        $psc_shoppage = (get_option('psc_shoppage_product_settings')) ? get_option('psc_shoppage_product_settings') : '';
        $psc_cartpage = (get_option('psc_cartpage_product_settings')) ? get_option('psc_cartpage_product_settings') : '';
        $psc_checkoutpage = (get_option('psc_checkoutpage_product_settings')) ? get_option('psc_checkoutpage_product_settings') : '';
        if ((is_single() && get_post_type() == 'psc_product') || $psc_shoppage == $post->ID || $psc_cartpage == $post->ID || $psc_checkoutpage == $post->ID) {
            get_sidebar();
        }
        if ($this->has_shortcode('psc_order_received') || $this->has_shortcode('psc_review_order')) {
            get_sidebar();
        }
    }

    public function psc_remove_body_singular_classes($classes) {
        global $post;
        if(!isset($post->ID)){
            return $classes;
        }
        $psc_shoppage = (get_option('psc_shoppage_product_settings')) ? get_option('psc_shoppage_product_settings') : '';
        $psc_cartpage = (get_option('psc_cartpage_product_settings')) ? get_option('psc_cartpage_product_settings') : '';
        $psc_checkoutpage = (get_option('psc_checkoutpage_product_settings')) ? get_option('psc_checkoutpage_product_settings') : '';
        if ((is_single() && get_post_type() == 'psc_product') || $psc_shoppage == $post->ID || $psc_cartpage == $post->ID || $psc_checkoutpage == $post->ID || $this->has_shortcode('psc_order_received') || $this->has_shortcode('psc_review_order')) {

            if (($key = array_search('singular', $classes)) !== false) {
                unset($classes[$key]);
            }
        }
        return $classes;
    }

    public function has_shortcode($shortcode = '') {

        $post_to_check = get_post(get_the_ID());
        $found = false;
        if (!$shortcode) {
            return $found;
        }
        if (stripos($post_to_check->post_content, '[' . $shortcode) !== false) {
            $found = true;
        }
        return $found;
    }

    public function psc_add_content_shortcode($content) {

        if (is_singular('psc_product') && get_post_type() == 'psc_product') {
            $content = '';
        }
        return $content;
    }

    public function psc_display_product_view() {
        try {
            psc_get_template('content-single-psc_product.php');
        } catch (Exception $ex) {
            
    }
}   

     public function psc_detect_shortcode() {
        if (is_singular('psc_product') && get_post_type() == 'psc_product') {
            global $post;
            if (!isset($post->post_content)) {
                return false;
            }
            if (!empty($post->post_content) && strpos($post->post_content, '[display_product]') !== false) {
                
            } else {
                $update_shortcode_post = array(
                    'ID' => $post->ID,
                    'post_content' => $post->post_content . '[display_product]',
                );
                $post->post_content = $post->post_content . '[display_product]';
                wp_update_post($update_shortcode_post);
                clean_post_cache($post->ID);
            }
        }
    }

}
