<?php

/**
 * @class       Paypal_Shopping_Cart_General_Setting
 * @version	1.0.0
 * @package	pal-shopping-cart
 * @category	Class
 * @author      wpgateways <wpgateways@gmail.com>
 */
class Paypal_Shopping_Cart_General_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('paypal_shopping_cart_psc_setting_general_tab_setting', array(__CLASS__, 'paypal_shopping_cart_psc_setting_general_tab_setting'));
        add_action('paypal_shopping_cart_psc_setting_general_tab_setting_save_field', array(__CLASS__, 'paypal_shopping_cart_psc_setting_general_tab_setting_save_field'));

        add_action('paypal_shopping_cart_psc_setting_product_tab_setting', array(__CLASS__, 'paypal_shopping_cart_psc_setting_product_tab_setting'));
        add_action('paypal_shopping_cart_psc_setting_product_tab_setting_save_field', array(__CLASS__, 'paypal_shopping_cart_psc_setting_product_tab_setting_save_field'));

        add_action('paypal_shopping_cart_psc_setting_checkout_tab_setting', array(__CLASS__, 'paypal_shopping_cart_psc_setting_checkout_tab_setting'));
        add_action('paypal_shopping_cart_psc_setting_checkout_tab_setting_save_field', array(__CLASS__, 'paypal_shopping_cart_psc_setting_checkout_tab_setting_save_field'));

        add_action('paypal_shopping_cart_psc_setting_emails_tab_setting', array(__CLASS__, 'paypal_shopping_cart_psc_setting_emails_tab_setting'));
        add_action('paypal_shopping_cart_psc_setting_emails_tab_setting_save_field', array(__CLASS__, 'paypal_shopping_cart_psc_setting_emails_tab_setting_save_field'));
    }

    public static function paypal_shopping_cart_psc_setting_general_tab_setting() {
        $psc_general_setting_fields = self::paypal_shopping_cart_psc_setting_general_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        ?>
        <form id="paypal_shopping_cart_setting_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($psc_general_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="paypal_shopping_cart_general_setting" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function paypal_shopping_cart_psc_setting_general_tab_setting_fields() {

        self::psc_currency_general_settings_selected_defualt();

        $fields[] = array('title' => __('General Options', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Currency', 'pal-shopping-cart'),
            'id' => 'psc_currency_general_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'options' => self::get_currency_dropdown()
        );

        $fields[] = array(
            'title' => __('Add to cart behaviour', 'pal-shopping-cart'),
            'id' => 'psc_addtocart_after_general_settings',
            'type' => 'select',
            'css' => 'width:25em;',
            'desc' => __('Redirect to the cart page after successful addition', 'pal-shopping-cart'),
            'options' => self::get_page_list_dropdown()
        );
         
        $fields[] = array(
            'title' => __('Enable Sidebar', 'pal-shopping-cart'),
            'id' => 'psc_enable_sidebar_general_settings',
            'type' => 'checkbox',
            'desc' => __('Enable sidebar in Shopping Cart page.', 'pal-shopping-cart'),
            'default' => 'no'
        );     

        $fields[] = array(
            'title' => __('Cart Page Button Text', 'pal-shopping-cart'),
            'id' => 'psc_cart_button_general_settings',
            'type' => 'text',
            'desc' => __('Cart page custom button text.', 'pal-shopping-cart'),
            'default' => 'Proceed to Checkout'
        );
        
        $fields[] = array(
            'title' => __('Checkout Page Button Text', 'pal-shopping-cart'),
            'id' => 'psc_checkout_button_general_settings',
            'type' => 'text',
            'desc' => __('Checkout page custom button text.', 'pal-shopping-cart'),
            'default' => 'Proceed to PayPal'
        );

        $fields[] = array(
            'title' => __('Coupons', 'pal-shopping-cart'),
            'id' => 'psc_coupons_general_settings',
            'type' => 'checkbox',
            'desc' => __('Enable the use of coupons in cart.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_setting_general_tab_setting_save_field() {
        $psc_general_setting_fields = self::paypal_shopping_cart_psc_setting_general_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        $Html_output->save_fields($psc_general_setting_fields);
    }

    public static function psc_currency_general_settings_selected_defualt() {

        $psc_currency_general_settings_selected = get_option('psc_currency_general_settings');
        if (isset($psc_currency_general_settings_selected) && empty($psc_currency_general_settings_selected)) {
            update_option('psc_currency_general_settings', 'USD');
        }
        return;
    }

    public static function paypal_shopping_cart_psc_setting_product_tab_setting() {
        $psc_product_setting_fields = self::paypal_shopping_cart_psc_setting_product_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        ?>
        <form id="paypal_shopping_cart_setting_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($psc_product_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="paypal_shopping_cart_product_setting" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function paypal_shopping_cart_psc_setting_product_tab_setting_fields() {

        $fields[] = array('title' => __('Shop & Product Pages', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('Shop Page', 'pal-shopping-cart'),
            'id' => 'psc_shoppage_product_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'options' => self::get_page_list_dropdown()
        );
        $fields[] = array(
            'title' => __('Cart Page', 'pal-shopping-cart'),
            'id' => 'psc_cartpage_product_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'options' => self::get_page_list_dropdown()
        );
        $fields[] = array(
            'title' => __('Checkout Page', 'pal-shopping-cart'),
            'id' => 'psc_checkoutpage_product_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'options' => self::get_page_list_dropdown()
        );
        $fields[] = array(
            'title' => __('Dispaly Product', 'pal-shopping-cart'),
            'id' => 'psc_shop_display_outofstock_product',
            'type' => 'checkbox',
            'default' => 'yes',
            'desc' => __('Display out of stock product in shop page.', 'pal-shopping-cart'),
        );
        $fields[] = array(
            'title' => __('Number of Column', 'pal-shopping-cart'),
            'id' => 'psc_column_product_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'options' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4')
        );
        $fields[] = array(
            'title' => __('Catalog Images', 'pal-shopping-cart'),
            'id' => 'psc_shop_catalog_image_size_width',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '300'
        );
        $fields[] = array(
            'id' => 'psc_shop_catalog_image_size_height',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '300'
        );
        $fields[] = array(
            'id' => 'psc_shop_catalog_image_size',
            'css' => '',
            'type' => 'checkbox',
            'default' => 'yes',
            'desc' => __('Hard Crop?', 'pal-shopping-cart'),
        );

        $fields[] = array(
            'title' => __('Single Product Image', 'pal-shopping-cart'),
            'id' => 'psc_shop_single_image_size_width',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '600'
        );
        $fields[] = array(
            'id' => 'psc_shop_single_image_size_height',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '600'
        );
        $fields[] = array(
            'id' => 'psc_shop_single_image_size',
            'css' => '',
            'type' => 'checkbox',
            'default' => 'yes',
            'desc' => __('Hard Crop?', 'pal-shopping-cart'),
        );

        $fields[] = array(
            'title' => __('Product Thumbnails', 'pal-shopping-cart'),
            'id' => 'psc_shop_thumbnail_image_size_width',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '180'
        );
        $fields[] = array(
            'id' => 'psc_shop_thumbnail_image_size_height',
            'css' => 'width:5em;',
            'type' => 'text',
            'default' => '180'
        );
        $fields[] = array(
            'id' => 'psc_shop_thumbnail_image_size',
            'css' => '',
            'type' => 'checkbox',
            'default' => 'yes',
            'desc' => __('Hard Crop?', 'pal-shopping-cart'),
        );


        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_setting_product_tab_setting_save_field() {
        $psc_product_setting_fields = self::paypal_shopping_cart_psc_setting_product_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        $Html_output->save_fields($psc_product_setting_fields);
    }

    public static function paypal_shopping_cart_psc_setting_emails_tab_setting() {
        $psc_email_setting_field = self::paypal_shopping_cart_psc_setting_emails_tab_email_setting_field();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($psc_email_setting_field); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function paypal_shopping_cart_psc_setting_emails_tab_setting_save_field() {
        $psc_email_setting_field = self::paypal_shopping_cart_psc_setting_emails_tab_email_setting_field();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        $Html_output->save_fields($psc_email_setting_field);
    }

    public static function paypal_shopping_cart_psc_setting_emails_tab_email_setting_field() {
        $email_body = "Hello %full_name%,
Thank you for Paypal Express Checkout Payment!

Your PayPal transaction ID is: %txn_id%
PayPal payment receiver email address: %receiver_email%
PayPal payment date: %payment_date%           
PayPal payment currency: %mc_currency%
PayPal payment amount: %mc_gross%

Thanks you very much,
Store Admin";
        update_option('psc_email_body_text_pre', trim($email_body));
        $settings = apply_filters('psc_email_settings', array(
            array('type' => 'sectionend', 'id' => 'email_recipient_options'),
            array('title' => __('Email settings', 'pal-shopping-cart'), 'type' => 'title', 'desc' => __('Set your own sender name and email address. Default WordPress values will be used if empty.', 'pal-shopping-cart'), 'id' => 'email_options'),
            array(
                'title' => __('Enable/Disable', 'pal-shopping-cart'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for customer', 'pal-shopping-cart'),
                'default' => 'yes',
                'id' => 'psc_donor_notification'
            ),
            array(
                'title' => __('Enable/Disable', 'pal-shopping-cart'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for website admin', 'pal-shopping-cart'),
                'default' => 'yes',
                'id' => 'psc_admin_notification'
            ),
            array(
                'title' => __('"From" Name', 'pal-shopping-cart'),
                'desc' => '',
                'id' => 'psc_email_from_name',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => esc_attr(get_bloginfo('title')),
                'autoload' => false
            ),
            array(
                'title' => __('"From" Email Address', 'pal-shopping-cart'),
                'desc' => '',
                'id' => 'psc_email_from_address',
                'type' => 'email',
                'custom_attributes' => array(
                    'multiple' => 'multiple'
                ),
                'css' => 'min-width:300px;',
                'default' => get_option('admin_email'),
                'autoload' => false
            ),
            array(
                'title' => __('Email subject', 'pal-shopping-cart'),
                'desc' => '',
                'id' => 'psc_email_subject',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => 'Thank you for Payment',
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_options'),
            array(
                'title' => __('Email body', 'pal-shopping-cart'),
                'desc' => __('The text to appear in the PayPal Shopping Cart Email. Please read more Help section(tab) for more dynamic tag', 'pal-shopping-cart'),
                'id' => 'psc_email_body_text',
                'css' => 'width:100%; height: 500px;',
                'type' => 'textarea',
                'editor' => 'false',
                'default' => $email_body,
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_template_options'),
        ));

        return $settings;
    }

    public static function paypal_shopping_cart_psc_setting_checkout_tab_setting() {
        $psc_checkout_setting_fields = self::paypal_shopping_cart_psc_setting_checkout_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        ?>
        <form id="paypal_shopping_cart_setting_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($psc_checkout_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="paypal_shopping_cart_checkout_setting" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <script type="text/javascript">
            jQuery('#psc_pec_testmode').change(function() {
                var sandbox = jQuery('#psc_pec_sandbox_api_username, #psc_pec_sandbox_api_password, #psc_pec_sandbox_api_signature').closest('tr'),
                        production = jQuery('#psc_pec_api_username, #psc_pec_api_password, #psc_pec_api_signature').closest('tr');
                if (jQuery(this).is(':checked')) {
                    sandbox.show();
                    production.hide();
                } else {
                    sandbox.hide();
                    production.show();
                }
            }).change();
            jQuery('#psc_pec_paypal_pro_testmode').change(function () {
                var sandbox = jQuery('#psc_pec_paypal_pro_sandbox_api_username, #psc_pec_paypal_pro_sandbox_api_password, #psc_pec_paypal_pro_sandbox_api_signature').closest('tr'),
                        production = jQuery('#psc_pec_paypal_pro_live_api_username, #psc_pec_paypal_pro_live_api_password, #psc_pec_paypal_pro_live_api_signature').closest('tr');
                if (jQuery(this).is(':checked')) {
                    sandbox.show();
                    production.hide();
                } else {
                    sandbox.hide();
                    production.show();
                }
            }).change();
            jQuery('#psc_pec_paypal_pro_payflow_testmode').change(function () {
                var sandbox = jQuery('#psc_pec_paypal_pro_payflow_sandbox_vendor, #psc_pec_paypal_pro_payflow_sandbox_password, #psc_pec_paypal_pro_payflow_sandbox_user').closest('tr'),
                        production = jQuery('#psc_pec_paypal_pro_payflow_live_vendor, #psc_pec_paypal_pro_payflow_live_password, #psc_pec_paypal_pro_payflow_live_user').closest('tr');
                if (jQuery(this).is(':checked')) {
                    sandbox.show();
                    production.hide();
                } else {
                    sandbox.hide();
                    production.show();
                }
            }).change();
            jQuery('#psc_pec_paypal_advanced_testmode').change(function () {
                var sandbox = jQuery('#psc_pec_paypal_advanced_sandbox_merchant, #psc_pec_paypal_advanced_sandbox_password, #psc_pec_paypal_advanced_sandbox_user, #psc_pec_paypal_advanced_sandbox_partner').closest('tr'),
                        production = jQuery('#psc_pec_paypal_advanced_live_merchant, #psc_pec_paypal_advanced_live_password, #psc_pec_paypal_advanced_live_user, #psc_pec_paypal_advanced_live_partner').closest('tr');

                if (jQuery(this).is(':checked')) {
                    sandbox.show();
                    production.hide();
                } else {

                    sandbox.hide();
                    production.show();
                }
            }).change();
        </script>
        <?php
    }

    public static function paypal_shopping_cart_psc_setting_checkout_tab_setting_fields() {

        $gateway_current_tab = (isset($_GET['gateway'])) ? $_GET['gateway'] : 'paypal_express_checkout';
        $fields = array();
        if ($gateway_current_tab == 'paypal_express_checkout') {
            $fields = self::paypal_shopping_cart_psc_paypal_express_checkout_setting_fields();
        } else if ($gateway_current_tab == 'paypal_pro') {
            $fields = self::paypal_shopping_cart_psc_paypal_pro_setting_fields();
        } else if ($gateway_current_tab == 'paypal_pro_payflow') {
            $fields = self::paypal_shopping_cart_psc_paypal_pro_payflow_setting_fields();
        } else if ($gateway_current_tab == 'paypal_advanced') {
            $fields = self::paypal_shopping_cart_psc_paypal_paypal_advanced_setting_fields();
        }
        return $fields;
    }

    public static function paypal_shopping_cart_psc_paypal_express_checkout_setting_fields() {

        $fields[] = array('title' => __('PayPal Express Checkout', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'psc_pec_methods');
        $fields[] = array(
            'title' => __('Enable/Disable', 'pal-shopping-cart'),
            'id' => 'psc_pec_enabled',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('Enable PayPal Express Checkout', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Title', 'pal-shopping-cart'),
            'id' => 'psc_pec_title',
            'desc' => __('This controls the title which the user sees during checkout.', 'pal-shopping-cart'),
            'type' => 'text',
            'default' => __('PayPal Express', 'pal-shopping-cart'),
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Description', 'pal-shopping-cart'),
            'id' => 'psc_pec_description',
            'css' => 'width:50%;',
            'type' => 'textarea',
            'default' => __('Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Enable/Disable Sandbox', 'pal-shopping-cart'),
            'id' => 'psc_pec_testmode',
            'type' => 'checkbox',
            'label' => __('Enable PayPal sandbox', 'pal-shopping-cart'),
            'default' => 'no',
            'desc' => __('The sandbox is PayPal\'s test environment and is only for use with sandbox accounts created within your <a href="http://developer.paypal.com" target="_blank">PayPal developer account</a>.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('API Username', 'pal-shopping-cart'),
            'id' => 'psc_pec_sandbox_api_username',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Create sandbox accounts and obtain API credentials from within your <a href="http://developer.paypal.com">PayPal developer account</a>.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('API Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_sandbox_api_password',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Signature', 'pal-shopping-cart'),
            'id' => 'psc_pec_sandbox_api_signature',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Username', 'pal-shopping-cart'),
            'id' => 'psc_pec_api_username',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Get your live account API credentials from your PayPal account profile under the API Access section or by using <a target="_blank" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run">this tool</a>', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('API Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_api_password',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Signature', 'pal-shopping-cart'),
            'id' => 'psc_pec_api_signature',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('Debug', 'pal-shopping-cart'),
            'id' => 'psc_pec_debug',
            'type' => 'checkbox',
            'desc' => __('Enable logging <code>/wp-content/uploads/pal-shopping-cart-logs/</code>', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Product Page', 'pal-shopping-cart'),
            'id' => 'psc_pec_single_product_enabled_button',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('Show the Express Checkout button on product detail pages, Allows customers to checkout using PayPal directly from a product page.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array(
            'title' => __('Cart Page', 'pal-shopping-cart'),
            'id' => 'psc_pec_cart_page_enabled_button',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('Show the Express Checkout button on cart pages, Allows customers to checkout using PayPal directly from a cart page.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array(
            'title' => __('Checkout Page', 'pal-shopping-cart'),
            'id' => 'psc_pec_checkout_page_enabled_button',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('Show the Express Checkout button on checkout pages, Allows customers to checkout using PayPal directly from a checkout page.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array(
            'title' => __('Standard checkout button', 'pal-shopping-cart'),
            'id' => 'psc_pec_standaed_checkout_button',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('Hide standard checkout button on cart page.', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Invoice Prefix', 'pal-shopping-cart'),
            'id' => 'psc_pec_invoice_id_prefix',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Add a prefix to the invoice ID sent to PayPal. This can resolve duplicate invoice problems when working with multiple websites on the same PayPal account.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('PayPal Account Optional', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_account_optional',
            'type' => 'checkbox',
            'default' => 'no',
            'desc' => __('PayPal Account Optional must be turned on in your PayPal account profile under Website Preferences.', 'pal-shopping-cart'),
        );
        $fields[] = array(
            'title' => __('Landing Page', 'pal-shopping-cart'),
            'id' => 'psc_pec_landing_page',
            'css' => 'width:10em;',
            'type' => 'select',
            'desc' => __('Type of PayPal page to display as default. PayPal Account Optional must be checked for this option to be used.', 'pal-shopping-cart'),
            'options' => array('login' => __('Login', 'pal-shopping-cart'),
                'billing' => __('Billing', 'pal-shopping-cart')),
            'default' => 'login',
        );
        $fields[] = array(
            'title' => __('Use WordPress Locale Code', 'pal-shopping-cart'),
            'id' => 'psc_pec_use_wp_locale_code',
            'type' => 'checkbox',
            'desc' => __('Pass the WordPress Locale Code setting to PayPal in order to display localized PayPal pages to buyers.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array(
            'title' => __('Brand Name', 'pal-shopping-cart'),
            'id' => 'psc_pec_brand_name',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('This controls what users see as the brand / company name on PayPal review pages.', 'pal-shopping-cart'),
            'default' => __(get_bloginfo('name'), 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('PayPal Checkout Logo (190x90px)', 'pal-shopping-cart'),
            'id' => 'psc_pec_checkout_logo',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('This controls what users see as the logo on PayPal review pages.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('PayPal Checkout Banner (750x90px)', 'pal-shopping-cart'),
            'id' => 'psc_pec_checkout_logo_hdrimg',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('This controls what users see as the header banner on PayPal review pages.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Customer Service Number', 'pal-shopping-cart'),
            'id' => 'psc_pec_customer_service_number',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('This controls what users see for your customer service phone number on PayPal review pages.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Express Checkout Message', 'pal-shopping-cart'),
            'id' => 'psc_pec_express_checkout_message',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('This message will be displayed checkout page.', 'pal-shopping-cart'),
            'default' => 'Skip the forms and pay faster with PayPal!'
        );
        $fields[] = array(
            'title' => __('Skip Final Review', 'pal-shopping-cart'),
            'id' => 'psc_pec_skip_final_review',
            'type' => 'checkbox',
            'desc' => __('By default, users will be returned from PayPal and presented with a final review page which includes shipping and tax in the order details.  Enable this option to eliminate this page in the checkout process.', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Transaction Type', 'pal-shopping-cart'),
            'id' => 'psc_pec_payment_action',
            'css' => 'width:10em;',
            'desc' => __('Sale will capture the funds immediately when the order is placed.  Authorization will authorize the payment but will not capture the funds.  You would need to capture funds through your PayPal account when you are ready to deliver.', 'pal-shopping-cart'),
            'type' => 'select',
            'options' => array(
                'Sale' => 'Sale',
                'Authorization' => 'Authorization',
            ),
            'default' => 'Sale'
        );
        $fields[] = array(
            'title' => __('Cancel Page', 'pal-shopping-cart'),
            'id' => 'psc_pec_cancel_page',
            'css' => 'width:10em;',
            'type' => 'select',
            'desc' => __('Sets the page users will be returned to if they click the Cancel link on the PayPal checkout pages.', 'pal-shopping-cart'),
            'options' => self::get_page_list_dropdown()
        );
        $fields[] = array(
            'title' => __('Send Item Details', 'pal-shopping-cart'),
            'id' => 'psc_pec_send_items',
            'css' => 'width:25em;',
            'type' => 'checkbox',
            'desc' => __('Include all line item details in the payment request to PayPal so that they can be seen from the PayPal transaction details page.', 'pal-shopping-cart'),
            'default' => 'yes'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_paypal_pro_setting_fields() {

        $fields[] = array('title' => __('PayPal Pro', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'psc_pec_paypal_pro_geteways');
        $fields[] = array(
            'title' => __('Enable/Disable', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_enabled',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('PayPal Pro Enable Gateway', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Title', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_title',
            'desc' => __('This controls the title which the user sees during checkout.', 'pal-shopping-cart'),
            'type' => 'text',
            'default' => __('PayPal Pro', 'pal-shopping-cart'),
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Description', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_description',
            'css' => 'width:50%;',
            'type' => 'textarea',
            'default' => __('Pay via PayPal Pro; you can pay with your credit card.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Enable/Disable Sandbox', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_testmode',
            'type' => 'checkbox',
            'label' => __('Enable PayPal sandbox', 'pal-shopping-cart'),
            'default' => 'no',
            'desc' => __('The sandbox is PayPal Pro test environment and is only for use with sandbox accounts created within your <a href="http://developer.paypal.com" target="_blank">PayPal developer account</a>.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('API Username', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_sandbox_api_username',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Create sandbox accounts and obtain API credentials from within your <a href="http://developer.paypal.com">PayPal developer account</a>.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('API Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_sandbox_api_password',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Signature', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_sandbox_api_signature',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Username', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_live_api_username',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Get your live account API credentials from your PayPal account profile under the API Access section or by using <a target="_blank" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_login-api-run">this tool</a>', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('API Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_live_api_password',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('API Signature', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_live_api_signature',
            'css' => 'width:25em;',
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('Payment Action', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_action',
            'css' => 'width:25em;',
            'type' => 'select',
            'desc' => __('Sale will capture the funds immediately when the order is placed. Authorization will authorize the payment but will not capture the funds. You would need to capture funds through your PayPal account when you are ready to deliver.', 'pal-shopping-cart'),
            'options' => array('Sale' => __('Sale', 'pal-shopping-cart'),
                'Authorization' => __('Authorization', 'pal-shopping-cart')),
            'default' => 'Sale',
        );
        $fields[] = array(
            'title' => __('Debug', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_debug',
            'type' => 'checkbox',
            'desc' => __('Enable logging <code>/wp-content/uploads/pal-shopping-cart-logs/</code>', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_paypal_pro_payflow_setting_fields() {
        $fields[] = array('title' => __('PayPal Pro Payflow', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'psc_pec_paypal_pro_payflow_geteways');
        $fields[] = array(
            'title' => __('Enable/Disable', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_enabled',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('PayPal Pro Payflow Enable Gateway', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Title', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_title',
            'desc' => __('This controls the title which the user sees during checkout.', 'pal-shopping-cart'),
            'type' => 'text',
            'default' => __('PayPal Pro Payflow', 'pal-shopping-cart'),
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Description', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_description',
            'css' => 'width:50%;',
            'type' => 'textarea',
            'default' => __('Pay via PayPal Pro Payflow; you can pay with your credit card.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Enable/Disable Sandbox', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_testmode',
            'type' => 'checkbox',
            'default' => 'no',
            'desc' => __('The sandbox is PayPal Pro Payflow test environment and is only for use with sandbox accounts created within your <a href="http://developer.paypal.com" target="_blank">PayPal developer account</a>.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('PayPal Vendor', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_sandbox_vendor',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Your merchant login ID that you created when you registered for the account.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('PayPal Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_sandbox_password',
            'css' => 'width:25em;',
            'desc' => __('The password that you defined while registering for the account.', 'pal-shopping-cart'),
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('PayPal User', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_sandbox_user',
            'css' => 'width:25em;',
            'desc' => __('If you set up one or more additional users on the account, this value is the ID of the user authorized to process transactions. Otherwise, leave this field blank.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('PayPal Vendor', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_live_vendor',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Your merchant login ID that you created when you registered for the account.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('PayPal Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_live_password',
            'css' => 'width:25em;',
            'desc' => __('The password that you defined while registering for the account.', 'pal-shopping-cart'),
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('PayPal User', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_live_user',
            'css' => 'width:25em;',
            'desc' => __('If you set up one or more additional users on the account, this value is the ID of the user authorized to process transactions. Otherwise, leave this field blank.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('PayPal Partner', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_partner',
            'css' => 'width:25em;',
            'desc' => __('The ID provided to you by the authorized PayPal Reseller who registered you for the Payflow SDK. If you purchased your account directly from PayPal, use PayPal or leave blank.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Payment Action', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_action',
            'css' => 'width:25em;',
            'type' => 'select',
            'desc' => __('Sale will capture the funds immediately when the order is placed. Authorization will authorize the payment but will not capture the funds. You would need to capture funds through your PayPal account when you are ready to deliver.', 'pal-shopping-cart'),
            'options' => array('Sale' => __('Sale', 'pal-shopping-cart'),
                'Authorization' => __('Authorization', 'pal-shopping-cart')),
            'default' => 'Sale',
        );
        $fields[] = array(
            'title' => __('Debug', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_pro_payflow_debug',
            'type' => 'checkbox',
            'desc' => __('Enable logging <code>/wp-content/uploads/pal-shopping-cart-logs/</code>', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_paypal_paypal_advanced_setting_fields() {
        $fields[] = array('title' => __('PayPal Advanced', 'pal-shopping-cart'), 'type' => 'title', 'desc' => '', 'id' => 'psc_pec_paypal_advanced_geteways');
        $fields[] = array(
            'title' => __('Enable/Disable', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_enabled',
            'css' => '',
            'type' => 'checkbox',
            'desc' => __('PayPal Advanced Enable Gateway', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('Title', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_title',
            'desc' => __('This controls the title which the user sees during checkout.', 'pal-shopping-cart'),
            'type' => 'text',
            'default' => __('PayPal Advanced', 'pal-shopping-cart'),
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Description', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_description',
            'css' => 'width:50%;',
            'type' => 'textarea',
            'default' => __('Pay via PayPal Advanced; you can pay with your credit card.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Enable/Disable Sandbox', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_testmode',
            'type' => 'checkbox',
            'default' => 'no',
            'desc' => __('The sandbox is PayPal Advanced test environment and is only for use with sandbox accounts created within your <a href="http://developer.paypal.com" target="_blank">PayPal developer account</a>.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Merchant Login', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_sandbox_merchant',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Your merchant login ID that you created when you registered for the account.', 'pal-shopping-cart'),
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_sandbox_password',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced account password.', 'pal-shopping-cart'),
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('User (or Merchant Login if no designated user is set up for the account)', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_sandbox_user',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced user account for this site.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('PayPal Partner', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_sandbox_partner',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced Partner. If you purchased the account directly from PayPal, use PayPal.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Merchant Login', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_live_merchant',
            'css' => 'width:25em;',
            'type' => 'text',
            'desc' => __('Your merchant login ID that you created when you registered for the account.', 'pal-shopping-cart')
        );
        $fields[] = array(
            'title' => __('Password', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_live_password',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced account password.', 'pal-shopping-cart'),
            'type' => 'password'
        );
        $fields[] = array(
            'title' => __('User (or Merchant Login if no designated user is set up for the account)', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_live_user',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced user account for this site.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('PayPal Partner', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_live_partner',
            'css' => 'width:25em;',
            'desc' => __('Enter your PayPal Advanced Partner. If you purchased the account directly from PayPal, use PayPal.', 'pal-shopping-cart'),
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Payment Action', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_action',
            'css' => 'width:25em;',
            'type' => 'select',
            'desc' => __('Sale will capture the funds immediately when the order is placed. Authorization will authorize the payment but will not capture the funds. You would need to capture funds through your PayPal account when you are ready to deliver.', 'pal-shopping-cart'),
            'options' => array('Sale' => __('Sale', 'pal-shopping-cart'),
                'Authorization' => __('Authorization', 'pal-shopping-cart')),
            'default' => 'Sale',
        );
        $fields[] = array(
            'title' => __('Layout', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_layout',
            'css' => 'width:25em;',
            'type' => 'select',
            'desc' => __('Layouts A and B redirect to PayPal\'s website for the user to pay.<br> Layout C (recommended) is a secure PayPal-hosted page but is embedded on your site using an iFrame.', 'pal-shopping-cart'),
            'options' => array('A' => __('Layout A', 'pal-shopping-cart'),
                'B' => __('Layout B', 'pal-shopping-cart'),
                'C' => __('Layout C', 'pal-shopping-cart')),
            'default' => 'A',
        );
        $fields[] = array(
            'title' => __('Mobile Mode', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_mobile_mode',
            'type' => 'checkbox',
            'desc' => __('Disable this option if your theme is not compatible with Mobile. Otherwise You would get Silent Post Error in Layout C.', 'pal-shopping-cart'),
            'default' => 'no'
        );

        $fields[] = array(
            'title' => __('Invoice Prefix', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_invoice_prefix',
            'css' => 'width:25em;',
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Page Collapse Border Color', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_page_collapse_bgcolor',
            'css' => 'width:25em;',
            'type' => 'text',
            'class' => 'psc_pec_paypal_advanced_color',
            'default' => '#1e73be'
        );
        $fields[] = array(
            'title' => __('Page Collapse Text Color', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_page_collapse_textcolor',
            'css' => 'width:25em;',
            'type' => 'text',
            'class' => 'psc_pec_paypal_advanced_color',
            'default' => '#8224e3'
        );
        $fields[] = array(
            'title' => __('Page Button Background Color', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_page_button_bgcolor',
            'css' => 'width:25em;',
            'type' => 'text',
            'class' => 'psc_pec_paypal_advanced_color',
            'default' => '#81d742'
        );
        $fields[] = array(
            'title' => __('Page Button Text Color', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_page_button_textcolor',
            'css' => 'width:25em;',
            'type' => 'text',
            'class' => 'psc_pec_paypal_advanced_color',
            'default' => '#eeee22'
        );
        $fields[] = array(
            'title' => __('Label Text Color', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_label_textcolor',
            'css' => 'width:25em;',
            'type' => 'text',
            'class' => 'psc_pec_paypal_advanced_color',
            'default' => '#dd9933'
        );
        $fields[] = array(
            'title' => __('Debug', 'pal-shopping-cart'),
            'id' => 'psc_pec_paypal_advanced_debug',
            'type' => 'checkbox',
            'desc' => __('Enable logging <code>/wp-content/uploads/pal-shopping-cart-logs/</code>', 'pal-shopping-cart'),
            'default' => 'no'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_shopping_cart_psc_setting_checkout_tab_setting_save_field() {
        $psc_checkout_setting_fields = self::paypal_shopping_cart_psc_setting_checkout_tab_setting_fields();
        $Html_output = new Paypal_Shopping_Cart_Html_output();
        $Html_output->save_fields($psc_checkout_setting_fields);
    }

    public static function get_page_list_dropdown() {
        try {
            $pages = get_pages();
            $result = array();
            $result['page'] = 'Select a page';
            foreach ($pages as $page) {
                $result[$page->ID] = $page->post_title;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function get_currency_dropdown() {

        $county_array_result = array(
            'AED' => __('United Arab Emirates Dirham', 'pal-shopping-cart'),
            'ARS' => __('Argentine Peso', 'pal-shopping-cart'),
            'AUD' => __('Australian Dollars', 'pal-shopping-cart'),
            'BDT' => __('Bangladeshi Taka', 'pal-shopping-cart'),
            'BRL' => __('Brazilian Real', 'pal-shopping-cart'),
            'BGN' => __('Bulgarian Lev', 'pal-shopping-cart'),
            'CAD' => __('Canadian Dollars', 'pal-shopping-cart'),
            'CLP' => __('Chilean Peso', 'pal-shopping-cart'),
            'CNY' => __('Chinese Yuan', 'pal-shopping-cart'),
            'COP' => __('Colombian Peso', 'pal-shopping-cart'),
            'CZK' => __('Czech Koruna', 'pal-shopping-cart'),
            'DKK' => __('Danish Krone', 'pal-shopping-cart'),
            'DOP' => __('Dominican Peso', 'pal-shopping-cart'),
            'EUR' => __('Euros', 'pal-shopping-cart'),
            'HKD' => __('Hong Kong Dollar', 'pal-shopping-cart'),
            'HRK' => __('Croatia kuna', 'pal-shopping-cart'),
            'HUF' => __('Hungarian Forint', 'pal-shopping-cart'),
            'ISK' => __('Icelandic krona', 'pal-shopping-cart'),
            'IDR' => __('Indonesia Rupiah', 'pal-shopping-cart'),
            'INR' => __('Indian Rupee', 'pal-shopping-cart'),
            'NPR' => __('Nepali Rupee', 'pal-shopping-cart'),
            'ILS' => __('Israeli Shekel', 'pal-shopping-cart'),
            'JPY' => __('Japanese Yen', 'pal-shopping-cart'),
            'KIP' => __('Lao Kip', 'pal-shopping-cart'),
            'KRW' => __('South Korean Won', 'pal-shopping-cart'),
            'MYR' => __('Malaysian Ringgits', 'pal-shopping-cart'),
            'MXN' => __('Mexican Peso', 'pal-shopping-cart'),
            'NGN' => __('Nigerian Naira', 'pal-shopping-cart'),
            'NOK' => __('Norwegian Krone', 'pal-shopping-cart'),
            'NZD' => __('New Zealand Dollar', 'pal-shopping-cart'),
            'PYG' => __('Paraguayan Guaraní', 'pal-shopping-cart'),
            'PHP' => __('Philippine Pesos', 'pal-shopping-cart'),
            'PLN' => __('Polish Zloty', 'pal-shopping-cart'),
            'GBP' => __('Pounds Sterling', 'pal-shopping-cart'),
            'RON' => __('Romanian Leu', 'pal-shopping-cart'),
            'RUB' => __('Russian Ruble', 'pal-shopping-cart'),
            'SGD' => __('Singapore Dollar', 'pal-shopping-cart'),
            'ZAR' => __('South African rand', 'pal-shopping-cart'),
            'SEK' => __('Swedish Krona', 'pal-shopping-cart'),
            'CHF' => __('Swiss Franc', 'pal-shopping-cart'),
            'TWD' => __('Taiwan New Dollars', 'pal-shopping-cart'),
            'THB' => __('Thai Baht', 'pal-shopping-cart'),
            'TRY' => __('Turkish Lira', 'pal-shopping-cart'),
            'UAH' => __('Ukrainian Hryvnia', 'pal-shopping-cart'),
            'USD' => __('US Dollars', 'pal-shopping-cart'),
            'VND' => __('Vietnamese Dong', 'pal-shopping-cart'),
            'EGP' => __('Egyptian Pound', 'pal-shopping-cart')
        );

        foreach ($county_array_result as $code => $name) {
            $county_array_result[$code] = $name . ' (' . self::get_paypal_shopping_cart_currency_symbol($code) . ')';
        }
        return $county_array_result;
    }

    public static function get_paypal_shopping_cart_currency_symbol($currency = '') {

        $currency_symbol = "";
        switch ($currency) {
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'AUD' :
            case 'ARS' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'HKD' :
            case 'MXN' :
            case 'NZD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'CHF' :
                $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'CNY' :
            case 'JPY' :
            case 'RMB' :
                $currency_symbol = '&yen;';
                break;
            case 'CZK' :
                $currency_symbol = '&#75;&#269;';
                break;
            case 'DKK' :
                $currency_symbol = 'DKK';
                break;
            case 'DOP' :
                $currency_symbol = 'RD&#36;';
                break;
            case 'EGP' :
                $currency_symbol = 'EGP';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'GBP' :
                $currency_symbol = '&pound;';
                break;
            case 'HRK' :
                $currency_symbol = 'Kn';
                break;
            case 'HUF' :
                $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' :
                $currency_symbol = 'Rp';
                break;
            case 'ILS' :
                $currency_symbol = '&#8362;';
                break;
            case 'INR' :
                $currency_symbol = 'Rs.';
                break;
            case 'ISK' :
                $currency_symbol = 'Kr.';
                break;
            case 'KIP' :
                $currency_symbol = '&#8365;';
                break;
            case 'KRW' :
                $currency_symbol = '&#8361;';
                break;
            case 'MYR' :
                $currency_symbol = '&#82;&#77;';
                break;
            case 'NGN' :
                $currency_symbol = '&#8358;';
                break;
            case 'NOK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'NPR' :
                $currency_symbol = 'Rs.';
                break;
            case 'PHP' :
                $currency_symbol = '&#8369;';
                break;
            case 'PLN' :
                $currency_symbol = '&#122;&#322;';
                break;
            case 'PYG' :
                $currency_symbol = '&#8370;';
                break;
            case 'RON' :
                $currency_symbol = 'lei';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'SEK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'THB' :
                $currency_symbol = '&#3647;';
                break;
            case 'TRY' :
                $currency_symbol = '&#8378;';
                break;
            case 'TWD' :
                $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'UAH' :
                $currency_symbol = '&#8372;';
                break;
            case 'VND' :
                $currency_symbol = '&#8363;';
                break;
            case 'ZAR' :
                $currency_symbol = '&#82;';
                break;
            default :
                $currency_symbol = '';
                break;
        }
        return $currency_symbol;
    }

}

Paypal_Shopping_Cart_General_Setting::init();