<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.premiumdev.com/
 * @since      1.0.0
 *
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Paypal_Shopping_Cart
 * @subpackage Paypal_Shopping_Cart/includes
 * @author     wpgateways <wpgateways@gmail.com>
 */
class Paypal_Shopping_Cart {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Paypal_Shopping_Cart_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public $psc_common_function;

    public function __construct() {

        $this->plugin_name = 'pal-shopping-cart';
        $this->version = '1.2.3';

        $this->init_hooks();
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        add_action('init', array($this, 'add_endpoint'), 0);
        add_action('parse_request', array($this, 'handle_api_requests'), 0);
        add_action('init', array($this, 'psc_check_version'), 5);
        add_action('paypal_shopping_cart_api_ipn_handler', array($this, 'paypal_shopping_cart_api_ipn_handler'));
        add_action('paypal_shopping_cart_api_psc_reviwe_order', array($this, 'paypal_shopping_cart_api_reviweorder'));
        add_action('paypal_shopping_cart_api_psc_place_order', array($this, 'paypal_shopping_cart_api_reviweorder'));
        add_action('paypal_shopping_cart_api_success', array($this, 'paypal_shopping_cart_api_success'));
        add_action('paypal_shopping_cart_api_processing', array($this, 'paypal_shopping_cart_api_psc_processing'));
        add_action('paypal_shopping_cart_api_cancelled', array($this, 'paypal_shopping_cart_api_psc_cancelled'));
        add_action('paypal_shopping_cart_api_completed', array($this, 'paypal_shopping_cart_api_psc_completed'));
        add_action('paypal_shopping_cart_api_view', array($this, 'paypal_shopping_cart_api_psc_view'));
        add_action('paypal_shopping_cart_api_paypal_express_checkout_method', array($this, 'paypal_shopping_cart_api_reviweorder'));
        add_action('psc_send_notification_mail', array($this, 'psc_send_notification_mail'), 10, 1);        
        add_action('save_post', array($this, 'paypal_shopping_cart_closed_comments'), 11, 1);
        add_action('http_api_curl', array($this, 'http_api_curl_ex_add_curl_parameter'), 10, 3);
    }

    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('edit.php?post_type=psc_order&page=psc_settings'), __('Configure', 'pal-shopping-cart')),
            'docs' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.premiumdev.com/product/paypal-shopping-cart/', __('Docs', 'pal-shopping-cart')),
            'support' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://wordpress.org/support/plugin/pal-shopping-cart/', __('Support', 'pal-shopping-cart')),
            'review' => sprintf('<a href="%s" target="_blank">%s</a>', 'https://wordpress.org/support/view/plugin-reviews/pal-shopping-cart', __('Write a Review', 'pal-shopping-cart')),
        );

        return array_merge($custom_actions, $actions);
    }

    public function init_hooks() {        
        add_action('after_setup_theme', array($this, 'setup_environment'));        
    }
    
    public function setup_environment() {
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . PSC_PLUGIN_BASENAME, array($this, 'plugin_action_links'), 10, 4);
        $this->add_image_sizes();
    }
    
    public function add_image_sizes() {
        $shop_thumbnail = psc_get_image_size('psc_shop_thumbnail');
        $shop_catalog = psc_get_image_size('psc_shop_catalog');
        $shop_single = psc_get_image_size('psc_shop_single');
        add_image_size('psc_shop_thumbnail', $shop_thumbnail['width'], $shop_thumbnail['height'], $shop_thumbnail['crop']);
        add_image_size('psc_shop_catalog', $shop_catalog['width'], $shop_catalog['height'], $shop_catalog['crop']);
        add_image_size('psc_shop_single', $shop_single['width'], $shop_single['height'], $shop_single['crop']);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Paypal_Shopping_Cart_Loader. Orchestrates the hooks of the plugin.
     * - Paypal_Shopping_Cart_i18n. Defines internationalization functionality.
     * - Paypal_Shopping_Cart_Admin. Defines all hooks for the admin area.
     * - Paypal_Shopping_Cart_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-paypal-shopping-cart-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-logger.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-paypal-shopping-cart-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-express-checkout.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-pro.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-pro-payflow.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-template-hooks.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-template-functions.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-core-functions.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-post-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-product-save-data.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-common-class.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-common-cart-function.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-ajax-add-cart-call.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-session-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-encrypt.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-string-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'templates/countries/countries.php';
        $this->loader = new Paypal_Shopping_Cart_Loader();
        $this->psc_common_function = new PSC_Common_Function();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Paypal_Shopping_Cart_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Paypal_Shopping_Cart_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Paypal_Shopping_Cart_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_admin, 'paypal_shopping_cart_custom_post');
        $this->loader->add_action('admin_menu', $plugin_admin, 'paypal_shopping_cart_custom_post_menu');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'paypal_shopping_cart_add_new_product');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'paypal_shopping_cart_add_new_order');
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'paypal_shopping_cart_product_coupon');
        $this->loader->add_action('save_post', $plugin_admin, 'paypal_shopping_cart_save_postdata', 10, 3);
        $this->loader->add_filter('manage_edit-psc_order_columns', $plugin_admin, 'set_custom_edit_psc_order_columns');
        $this->loader->add_action('manage_psc_order_posts_custom_column', $plugin_admin, 'custom_psc_order_columns', 10, 2);
        $this->loader->add_filter('manage_edit-psc_order_sortable_columns', $plugin_admin, 'psc_order_table_sorting');
        $this->loader->add_filter('manage_edit-psc_product_columns', $plugin_admin, 'set_custom_edit_psc_product_columns');
        $this->loader->add_action('manage_psc_product_posts_custom_column', $plugin_admin, 'custom_psc_product_columns', 10, 2);
        $this->loader->add_filter('manage_edit-psc_product_sortable_columns', $plugin_admin, 'psc_product_table_sorting');
        $this->loader->add_filter('manage_edit-psc_coupon_columns', $plugin_admin, 'set_custom_edit_psc_coupon_columns');
        $this->loader->add_action('manage_psc_coupon_posts_custom_column', $plugin_admin, 'custom_psc_coupon_columns', 10, 2);
        $this->loader->add_filter('enter_title_here', $plugin_admin, 'paypal_shopping_cart_product_coupon_view_place_holder', 20, 2);
        $this->loader->add_action('save_post', $plugin_admin, 'paypal_shopping_cart_save_coupons_fields', 10, 3);
        $this->loader->add_action('current_screen', $plugin_admin, 'psc_comments_exclude_lazy_hook', 10, 2);
        $this->loader->add_filter('wp_count_comments', $plugin_admin, 'wp_count_comments', 10, 2);
        $this->loader->add_action( 'init', $plugin_admin, 'register_post_status', 9 );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Paypal_Shopping_Cart_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        // $this->loader->add_filter('template_include', $plugin_public, 'paypal_shopping_cart_get_shoppage');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function handle_api_requests() {
        global $wp;

        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'PayPal_Express_Checkout_Method') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'review_order') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field('psc_reviwe_order');
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'place-order') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field('psc_place_order');
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'Success') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'processing') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'cancelled') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'completed') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'view') {
            $wp->query_vars['Paypal_Shopping_Cart'] = sanitize_text_field($_GET['psc_action']);
        }
        if (isset($_GET['action']) && $_GET['action'] == 'ipn_handler') {
            $wp->query_vars['Paypal_Shopping_Cart'] = $_GET['action'];
        } 
        
        if (!empty($wp->query_vars['Paypal_Shopping_Cart'])) {
            ob_start();
            $api = strtolower(esc_attr($wp->query_vars['Paypal_Shopping_Cart']));
            do_action('paypal_shopping_cart_api_' . strtolower($api));
            ob_end_clean();
            die('1');
        }
    }

    public function add_endpoint() {
        add_rewrite_endpoint('Paypal_Shopping_Cart', EP_ALL);
    }

    public function paypal_shopping_cart_api_reviweorder() {
        if (!class_exists('Paypal_Shopping_Cart_Express_Checkout')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-express-checkout.php';
        }
        $Paypal_Shopping_Cart_Express_Checkout = new Paypal_Shopping_Cart_Express_Checkout();
        $Paypal_Shopping_Cart_Express_Checkout->paypal_express_checkout($_GET);
    }

    public function paypal_shopping_cart_api_success() {

        if (!class_exists('Paypal_Shopping_Cart_Express_Checkout')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/paypal-shopping-cart-paypal-express-checkout.php';
        }
        $Paypal_Shopping_Cart_Express_Checkout = new Paypal_Shopping_Cart_Express_Checkout();
        $Paypal_Shopping_Cart_Express_Checkout->order_complete($_GET);
    }

    public function paypal_shopping_cart_api_psc_processing() {
        if (!class_exists('Paypal_Shopping_Cart_Order_Action_Now')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/paypal-shopping-cart-order-action.php';
        }
        $Paypal_Shopping_Cart_Order_Action_Now = new Paypal_Shopping_Cart_Order_Action_Now();
        $Paypal_Shopping_Cart_Order_Action_Now->order_change_status($_GET);
    }

    public function paypal_shopping_cart_api_psc_cancelled() {
        if (!class_exists('Paypal_Shopping_Cart_Order_Action_Now')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/paypal-shopping-cart-order-action.php';
        }
        $Paypal_Shopping_Cart_Order_Action_Now = new Paypal_Shopping_Cart_Order_Action_Now();
        $Paypal_Shopping_Cart_Order_Action_Now->order_change_status($_GET);
    }

    public function paypal_shopping_cart_api_psc_completed() {
        if (!class_exists('Paypal_Shopping_Cart_Order_Action_Now')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/paypal-shopping-cart-order-action.php';
        }
        $Paypal_Shopping_Cart_Order_Action_Now = new Paypal_Shopping_Cart_Order_Action_Now();
        $Paypal_Shopping_Cart_Order_Action_Now->order_change_status($_GET);
    }

    public function paypal_shopping_cart_api_psc_view() {
        if (!class_exists('Paypal_Shopping_Cart_Order_Action_Now')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/paypal-shopping-cart-order-action.php';
        }
        $Paypal_Shopping_Cart_Order_Action_Now = new Paypal_Shopping_Cart_Order_Action_Now();
        $Paypal_Shopping_Cart_Order_Action_Now->order_change_status($_GET);
    }

    public function paypal_shopping_cart_api_ipn_handler() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-shopping-cart-paypal-listner.php';
        $PayPal_Shopping_Cart_PayPal_listner = new PayPal_Shopping_Cart_PayPal_listner();
        if ($PayPal_Shopping_Cart_PayPal_listner->check_ipn_request()) {
            $PayPal_Shopping_Cart_PayPal_listner->successful_request($IPN_status = true);
        } else {
            $PayPal_Shopping_Cart_PayPal_listner->successful_request($IPN_status = false);
        }
    }

    public function psc_send_notification_mail($posted_result_email) {

        $posted = $posted_result_email;
        $template = get_option('psc_email_body_text');
        if (isset($template) && empty($template)) {
            $template = get_option('psc_email_body_text_pre');
        }
        $parse_templated = $this->psc_template_vars_replacement($template, $posted);
        $from_name = get_option('psc_email_from_name');
        $from_name_value = isset($from_name) ? $from_name : 'From';
        $sender_address = get_option('psc_email_from_address');
        $sender_address_value = isset($sender_address) ? $sender_address : get_option('admin_email');
        if (isset($from_name_value) && !empty($from_name_value)) {
            $headers = "From: " . $from_name_value . " <" . $sender_address_value . ">";
        }

        if (isset($posted['payeremail']) && !empty($posted['payeremail'])) {
            $subject = get_option('psc_email_subject');
            $subject_value = isset($subject) ? $subject : 'Thank you for Payment.';
            $enable_admin = (get_option('psc_admin_notification')) ? get_option('psc_admin_notification') : 'yes';
            $admin_email = get_option('admin_email');
            if (isset($headers) && !empty($headers)) {

                $user_mail_status = wp_mail($posted['payeremail'], $subject_value, $parse_templated, $headers);
                $this->email_send_status($user_mail_status, $posted['payeremail'], 'user');
                if ($enable_admin) {
                    $admin_mail_status = wp_mail($admin_email, $subject_value, $parse_templated, $headers);
                    $this->email_send_status($admin_mail_status, $admin_email, 'admin');
                }
            } else {

                $user_mail_status = wp_mail($posted['payeremail'], $subject_value, $parse_templated);
                $this->email_send_status($user_mail_status, $posted['payeremail'], 'User');
                if ($enable_admin) {
                    $admin_mail_status = wp_mail($admin_email, $subject_value, $parse_templated);
                    $this->email_send_status($admin_mail_status, $admin_email, 'Admin');
                }
            }
        }
    }

    public function email_send_status($status, $mail_receiver_id, $is_mail_receiver) {

        $PSC_LOG = new Paypal_Shopping_Cart_Logger();

        if (isset($status) && '0' == $status) {
            $PSC_LOG->add("email_details", 'Mail Delivery Person: ' . $is_mail_receiver);
            $PSC_LOG->add("email_details", 'Mail Delivery: Failure');
            $PSC_LOG->add("email_details", 'Mail Receiver Email:' . $mail_receiver_id);
        } else if (isset($status) && '1' == $status) {
            $PSC_LOG->add("email_details", 'Mail Delivery Person: ' . $is_mail_receiver);
            $PSC_LOG->add("email_details", 'Mail Delivery Status: Success');
            $PSC_LOG->add("email_details", 'Mail Receiver Email: ' . $mail_receiver_id);
        }

        return;
    }

    public function psc_template_vars_replacement($template, $posted) {

        $to_replace = array(
            'blog_url' => get_option('siteurl'),
            'home_url' => get_option('home'),
            'blog_name' => get_option('blogname'),
            'blog_description' => get_option('blogdescription'),
            'admin_email' => get_option('admin_email'),
            'date' => date_i18n(get_option('date_format')),
            'time' => date_i18n(get_option('time_format')),
            'txn_id' => $posted['txn_id'],
            'receiver_email' => $posted['USER'],
            'full_name' => $posted['PAYMENTREQUEST_0_SHIPTONAME'],
            'payment_date' => $posted['payment_date'],
            'mc_currency' => $posted['PAYMENTREQUEST_0_CURRENCYCODE'],
            'mc_gross' => $posted['PAYMENTREQUEST_0_AMT']
        );
        foreach ($to_replace as $tag => $var) {

            $template = str_replace('%' . $tag . '%', $var, $template);
        }
        return $template;
    }

    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Paypal_Shopping_Cart_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    public function paypal_shopping_cart_closed_comments($post_id) {

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (get_post_type() == 'psc_product') {
            remove_action('save_post', array($this, 'paypal_shopping_cart_closed_comments'), 11, 1);
            $my_comment_status = array(
                'ID' => $post_id,
                'comment_status' => 'closed'
            );
            wp_update_post($my_comment_status);
        }
    }

    public function psc_check_version() {
        global $wpdb;
        if (get_option('psc_version') !== $this->version) {
            $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_title LIKE '%s'", 'Shop'));
            $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_title LIKE '%s'", 'Cart'));
            $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_title LIKE '%s'", 'Checkout'));
            $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_title LIKE '%s'", 'Review Order'));
            $wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_title LIKE '%s'", 'Order Received'));
            $psc_pages = array('Shop', 'Cart', 'Checkout', 'pscrevieworder', 'pscordercomplete');
            foreach ($psc_pages as $key => $value) {
                $page = get_page_by_title($value);                
                if( isset($page) && 'null' != $page ){
                    wp_delete_post($page->ID, true);
                } 
            }
            update_option('psc_shoppage_product_settings', '');
            update_option('psc_cartpage_product_settings', '');
            update_option('psc_checkoutpage_product_settings', '');
            update_option('psc_pec_cancel_page', '');
            update_option('psc_version', $this->version);
        }
        $this->psc_common_function->create_paypal_shopping_cart_page();
    }
    public function http_api_curl_ex_add_curl_parameter($handle, $r, $url ) {
        if ( strstr( $url, 'https://' ) && strstr( $url, '.paypal.com' ) ) {
            curl_setopt($handle, CURLOPT_VERBOSE, 1);
            curl_setopt($handle, CURLOPT_SSLVERSION, 6);
        }
    }

}