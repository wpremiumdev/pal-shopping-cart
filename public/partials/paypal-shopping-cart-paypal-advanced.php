<?php

class Paypal_Shopping_Cart_PayPal_Advanced {

    public $PSC_Common_Function_OBJ;
    public $PSC_Common_Cart_Function_OBJ;

    public function __construct() {

        $this->PSC_Common_Function_OBJ = new PSC_Common_Function();
        $this->PSC_Common_Cart_Function_OBJ = new PSC_Common_Cart_Function();

        $this->id = 'paypal_advanced';
        $this->api_version = '120';
        $this->title = (get_option('psc_pec_paypal_advanced_title')) ? get_option('psc_pec_paypal_advanced_title') : '';
        $this->description = (get_option('psc_pec_paypal_advanced_description')) ? get_option('psc_pec_paypal_advanced_description') : '';
        $this->testmode = (get_option('psc_pec_paypal_advanced_testmode')) ? TRUE : FALSE;
        $this->paymentaction = (get_option('psc_pec_paypal_advanced_action')) ? get_option('psc_pec_paypal_advanced_action') : 'Sale';
        $this->layout_code = (get_option('psc_pec_paypal_advanced_layout')) ? get_option('psc_pec_paypal_advanced_layout') : 'A';
        $this->mobile_mode = (get_option('psc_pec_paypal_advanced_mobile_mode')) ? TRUE : FALSE;
        $this->invoice_prifix = (get_option('psc_pec_paypal_advanced_invoice_prefix')) ? get_option('psc_pec_paypal_advanced_invoice_prefix') : '';
        $this->page_collapse_bgcolor = (get_option('psc_pec_paypal_advanced_page_collapse_bgcolor')) ? get_option('psc_pec_paypal_advanced_page_collapse_bgcolor') : '#1e73be';
        $this->page_collapse_textcolor = (get_option('psc_pec_paypal_advanced_page_collapse_textcolor')) ? get_option('psc_pec_paypal_advanced_page_collapse_textcolor') : '#81d742';
        $this->page_button_bgcolor = (get_option('psc_pec_paypal_advanced_page_button_bgcolor')) ? get_option('psc_pec_paypal_advanced_page_button_bgcolor') : '#dd9933';
        $this->page_button_textcolor = (get_option('psc_pec_paypal_advanced_page_button_textcolor')) ? get_option('psc_pec_paypal_advanced_page_button_textcolor') : '#dd3333';
        $this->label_textcolor = (get_option('psc_pec_paypal_advanced_label_textcolor')) ? get_option('psc_pec_paypal_advanced_label_textcolor') : '#8224e3';
        $this->debug = (get_option('psc_pec_paypal_advanced_debug')) ? TRUE : FALSE;
        $this->currency = (get_option('psc_currency_general_settings')) ? get_option('psc_currency_general_settings') : 'USD';

        $this->securetoken = '';
        $this->secure_token_id = '';
        $this->parsed_response = '';
        $this->is_mode = '';
        $this->layout = '';
        $this->order_id = '';
        $this->order_key = '';
        $this->returnURL = '';
        $this->buttonsource = 'mbjtechnolabs_SP';
        $this->order_received_URL = '';

        $this->home_url = is_ssl() ? home_url('/', 'https') : home_url('/');
        $this->checkout_page = get_permalink($this->PSC_Common_Function_OBJ->psc_checkout_page());
        $this->cart_page = get_permalink($this->PSC_Common_Function_OBJ->psc_cart_page());
        
        $psc_order_id = $this->PSC_Common_Function_OBJ->psc_get_page_id_by_title('Order Received');
        if (isset($psc_order_id) && !empty($psc_order_id)) {
            $this->order_received_URL = get_permalink($psc_order_id->ID);
        }

        //$this->returnURL = add_query_arg(array('psc-advanced-api' => 'PSCPaypalAdvanced', 'completed' => 'true'), $this->checkout_page);
        if ($this->testmode) {
            $this->is_mode = 'TEST';
            $this->Paypal_URL = "https://pilot-payflowpro.paypal.com";
            $this->paypal_vendor = (get_option('psc_pec_paypal_advanced_sandbox_merchant')) ? trim(get_option('psc_pec_paypal_advanced_sandbox_merchant')) : '';
            $this->paypal_password = (get_option('psc_pec_paypal_advanced_sandbox_password')) ? trim(get_option('psc_pec_paypal_advanced_sandbox_password')) : '';
            $this->paypal_user = (get_option('psc_pec_paypal_advanced_sandbox_user')) ? trim(get_option('psc_pec_paypal_advanced_sandbox_user')) : '';
            $this->paypal_partner = (get_option('psc_pec_paypal_advanced_sandbox_partner')) ? trim(get_option('psc_pec_paypal_advanced_sandbox_partner')) : 'PayPal';
        } else {
            $this->is_mode = 'LIVE';
            $this->Paypal_URL = "https://payflowpro.paypal.com";
            $this->paypal_vendor = (get_option('psc_pec_paypal_advanced_live_merchant')) ? trim(get_option('psc_pec_paypal_advanced_live_merchant')) : '';
            $this->paypal_password = (get_option('psc_pec_paypal_advanced_live_password')) ? trim(get_option('psc_pec_paypal_advanced_live_password')) : '';
            $this->paypal_user = (get_option('psc_pec_paypal_advanced_live_user')) ? trim(get_option('psc_pec_paypal_advanced_live_user')) : '';
            $this->paypal_partner = (get_option('psc_pec_paypal_advanced_live_partner')) ? trim(get_option('psc_pec_paypal_advanced_live_partner')) : 'PayPal';
        }

        switch (strtoupper($this->layout_code)) {
            case 'A': $this->layout = 'TEMPLATEA';
                break;
            case 'B': $this->layout = 'TEMPLATEB';
                break;
            case 'C': $this->layout = 'MINLAYOUT';
                break;
        }
        add_action('pal_shopping_cart_get_receipt_hook', array($this, 'pal_shopping_cart_paypal_advanced_receipt_page'), 10, 1);
    }

    public function pal_shopping_cart_paypal_advanced_process_payment($posted) {
        try {
            $is_posted_array = $this->pal_shopping_cart_paypal_advanced_is_posted_array($posted);
            if ($is_posted_array) {
                $this->pal_shopping_cart_paypal_advanced_process($posted);
            } else {
                $URL = $this->checkout_page;
                $message = 'Posted Array is Empty! Please Try Again.';
                $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
            }
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_is_posted_array($posted) {
        try {
            $result = FALSE;
            if (is_array($posted) && count($posted) > 0) {
                $result = TRUE;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_process($posted) {
        try {
            $this->order_id = $this->PSC_Common_Function_OBJ->create_order();

            $this->PSC_Common_Function_OBJ->session_set('order_id', $this->order_id);
            $this->PSC_Common_Function_OBJ->session_set('order_key', $this->order_key);

            $this->pal_shopping_cart_paypal_advanced_update_post_meta($posted);
            $this->pal_shopping_cart_paypal_advanced_secure_token($posted);

            if ($this->securetoken != "") {
                update_post_meta($this->order_id, '_secure_token_id', $this->secure_token_id);
                update_post_meta($this->order_id, '_secure_token', $this->securetoken);
                $pay_url = add_query_arg(array('order-pay' => $this->order_id, 'key' => $this->order_key, 'psc-order-pay' => 'Receipt'), $this->checkout_page);
                wp_redirect($pay_url);
                exit;
            }
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_update_post_meta($posted) {

        if (empty($this->order_id)) {
            return;
        }

        update_post_meta($this->order_id, '_shipping_first_name', $posted['billing_first_name']);
        update_post_meta($this->order_id, '_shipping_last_name', $posted['billing_last_name']);
        update_post_meta($this->order_id, '_shipping_full_name', $posted['billing_first_name'] . ' ' . $posted['billing_last_name']);
        update_post_meta($this->order_id, '_shipping_company', $posted['billing_company']);
        update_post_meta($this->order_id, '_billing_phone', $posted['billing_phone']);
        update_post_meta($this->order_id, '_shipping_address_1', $posted['billing_address_1']);
        update_post_meta($this->order_id, '_shipping_address_2', $posted['billing_address_2']);
        update_post_meta($this->order_id, '_shipping_city', $posted['billing_city']);
        update_post_meta($this->order_id, '_shipping_postcode', $posted['billing_postcode']);
        update_post_meta($this->order_id, '_shipping_country', $posted['billing_country']);
        update_post_meta($this->order_id, '_shipping_state', $posted['billing_state']);
        update_post_meta($this->order_id, '_customer_user', get_current_user_id());
        $this->order_key = apply_filters('psc_generate_order_key', uniqid('order_'));
        $psc_cart_serialize = serialize($this->PSC_Common_Function_OBJ->session_cart_contents());
        update_post_meta($this->order_id, '_uniq_id_generatore', $this->order_key);
        update_post_meta($this->order_id, '_psc_cart_subtotal', $this->PSC_Common_Function_OBJ->psc_calculate_cart_totals());
        update_post_meta($this->order_id, '_order_cart_discount', serialize($this->PSC_Common_Function_OBJ->get_coupon_discount_and_code()));
        update_post_meta($this->order_id, '_psc_cart_total', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_total());
        update_post_meta($this->order_id, '_currencycode', $this->currency);
        update_post_meta($this->order_id, '_psc_cart_serialize', $psc_cart_serialize);
        update_post_meta($this->order_id, '_orderdate', date('j F, Y'));
        update_post_meta($this->order_id, '_payment_method_title', $this->title);
        update_post_meta($this->order_id, '_payment_method', $this->id);
        update_post_meta($this->order_id, '_order_responce_total_tax', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_tax_total());
        update_post_meta($this->order_id, '_order_responce_total_shipping', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_shipping_total());

        if (is_user_logged_in()) {
            $userLogined = wp_get_current_user();
            update_post_meta($this->order_id, '_billing_email', $userLogined->user_email);
        } else {
            update_post_meta($this->order_id, '_billing_email', $posted['billing_email']);
        }

        return true;
    }

    public function pal_shopping_cart_paypal_advanced_secure_token($posted) {
        try {

            $this->secure_token_id = uniqid(substr($_SERVER['HTTP_HOST'], 0, 9), true);
            $paypal_args = array();
            if (($this->layout == 'MINLAYOUT' || $this->layout == 'C') && $this->mobile_mode == "yes") {
                $template = wp_is_mobile() ? "MOBILE" : $this->layout;
            } else {
                $template = $this->layout;
            }

            $this->returnURL = add_query_arg(array('psc_action' => 'order_received', 'order' => $this->order_key, 'status' => 'completed'), $this->order_received_URL);
            $paypal_args = array(
                'VERBOSITY' => 'HIGH',
                'USER' => $this->paypal_user,
                'VENDOR' => $this->paypal_vendor,
                'PARTNER' => $this->paypal_partner,
                'PWD[' . strlen($this->paypal_password) . ']' => $this->paypal_password,
                'SECURETOKENID' => $this->secure_token_id,
                'CREATESECURETOKEN' => 'Y',
                'TRXTYPE' => $this->paymentaction,
                'CUSTREF' => $this->order_key,
                'USER1' => $this->order_id,
                'INVNUM' => $this->invoice_prifix . '' . $this->order_key,
                'AMT' => $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_total(),
                'FREIGHTAMT' => $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_shipping_total(),
                'COMPANYNAME[' . strlen($posted['billing_company']) . ']' => $posted['billing_company'],
                'CURRENCY' => $this->currency,
                'EMAIL' => $posted['billing_email'],
                'BILLTOFIRSTNAME[' . strlen($posted['billing_first_name']) . ']' => $posted['billing_first_name'],
                'BILLTOLASTNAME[' . strlen($posted['billing_last_name']) . ']' => $posted['billing_last_name'],
                'BILLTOSTREET[' . strlen($posted['billing_address_1'] . ' ' . $posted['billing_address_2']) . ']' => $posted['billing_address_1'] . ' ' . $posted['billing_address_2'],
                'BILLTOCITY[' . strlen($posted['billing_city']) . ']' => $posted['billing_city'],
                'BILLTOSTATE[' . strlen($posted['billing_state']) . ']' => $posted['billing_state'],
                'BILLTOZIP' => $posted['billing_postcode'],
                'BILLTOCOUNTRY[' . strlen($posted['billing_country']) . ']' => $posted['billing_country'],
                'BILLTOEMAIL' => $posted['billing_email'],
                'BILLTOPHONENUM' => $posted['billing_phone'],
                'SHIPTOFIRSTNAME[' . strlen($posted['billing_first_name']) . ']' => $posted['billing_first_name'],
                'SHIPTOLASTNAME[' . strlen($posted['billing_last_name']) . ']' => $posted['billing_last_name'],
                'SHIPTOSTREET[' . strlen($posted['billing_address_1'] . ' ' . $posted['billing_address_2']) . ']' => $posted['billing_address_1'] . ' ' . $posted['billing_address_2'],
                'SHIPTOCITY[' . strlen($posted['billing_city']) . ']' => $posted['billing_city'],
                'SHIPTOZIP' => $posted['billing_postcode'],
                'SHIPTOCOUNTRY[' . strlen($posted['billing_country']) . ']' => $posted['billing_country'],
                'BUTTONSOURCE' => $this->buttonsource,
                'RETURNURL[' . strlen($this->returnURL) . ']' => $this->returnURL,
                'URLMETHOD' => 'POST',
                'TEMPLATE' => $template,
                'PAGECOLLAPSEBGCOLOR' => ltrim($this->page_collapse_bgcolor, '#'),
                'PAGECOLLAPSETEXTCOLOR' => ltrim($this->page_collapse_textcolor, '#'),
                'PAGEBUTTONBGCOLOR' => ltrim($this->page_button_bgcolor, '#'),
                'PAGEBUTTONTEXTCOLOR' => ltrim($this->page_button_textcolor, '#'),
                'LABELTEXTCOLOR' => ltrim($this->label_textcolor, '#')
            );

            if (empty($posted['billing_state'])) {
                $paypal_args['SHIPTOSTATE[' . strlen($posted['billing_city']) . ']'] = $posted['billing_city'];
            } else {
                $paypal_args['SHIPTOSTATE[' . strlen($posted['billing_state']) . ']'] = $posted['billing_state'];
            }
            // Determine the ERRORURL,CANCELURL and SILENTPOSTURL
            $cancelurl = add_query_arg('psc-advanced-api', 'PSCPaypalAdvanced', add_query_arg('cancel', 'true', $this->home_url));
            $paypal_args['CANCELURL[' . strlen($cancelurl) . ']'] = $cancelurl;

            $errorurl = add_query_arg('psc-advanced-api', 'PSCPaypalAdvanced', add_query_arg('error', 'true', $this->home_url));
            $paypal_args['ERRORURL[' . strlen($errorurl) . ']'] = $errorurl;

            $silentposturl = add_query_arg('psc-advanced-api', 'PSCPaypalAdvanced', add_query_arg('silent', 'true', $this->home_url));
            $paypal_args['SILENTPOSTURL[' . strlen($silentposturl) . ']'] = $silentposturl;

            $paypal_args['TAXAMT'] = 0;
            $paypal_args['FEES'] = 0;
            $paypal_args['ITEMAMT'] = 0;

            // Cart Contents
            $item_loop = 0;
            if (sizeof($this->PSC_Common_Function_OBJ->session_cart_contents()) > 0) {
                foreach ($this->PSC_Common_Function_OBJ->session_cart_contents() as $key => $item) {
                    if ($item['qty']) {
                        $paypal_args['L_NAME' . $item_loop . '[' . strlen($item['name']) . ']'] = $item['name'];
                        $paypal_args['L_QTY' . $item_loop] = $item['qty'];
                        $paypal_args['L_COST' . $item_loop] = $item['price']; /* No Tax , No Round) */
                        //$paypal_args['L_TAXAMT' . $item_loop] = $item['tax']; /* No Round it */
                        $paypal_args['ITEMAMT'] += $item['price']; /* No tax, No Round */
                        $item_loop++;
                    }
                }
            }
            $postData = '';
            $logData = '';

            foreach ($paypal_args as $key => $val) {
                $postData .='&' . $key . '=' . $val;
                if (strpos($key, 'PWD') === 0)
                    $logData .='&PWD=XXXX';
                else
                    $logData .='&' . $key . '=' . $val;
            }
            $postData = trim($postData, '&');
            $response = wp_remote_post($this->Paypal_URL, array(
                'method' => 'POST',
                'body' => $postData,
                'timeout' => 70,
                'user-agent' => 'pal-shopping-cart',
                'httpversion' => '1.1',
                'headers' => array('host' => 'www.paypal.com')
            ));

            if (is_wp_error($response)) {
                $this->pal_shopping_cart_write_activity_log_permition($response->get_error_message());
                $URL = $this->cart_page;
                $message = $response->get_error_message();
                $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
            }
            parse_str($response['body'], $this->parsed_response);
            if (isset($this->parsed_response['RESPMSG']) && in_array($this->parsed_response['RESULT'], array(0, 126, 127))) {
                $this->pal_shopping_cart_write_activity_log_permition($this->parsed_response);
                $this->securetoken = $this->parsed_response['SECURETOKEN'];
                $this->secure_token_id = $this->parsed_response['SECURETOKENID'];
                return TRUE;
            } else {
                $this->pal_shopping_cart_write_activity_log_permition($this->parsed_response);
                $URL = $this->cart_page;
                $message = $this->parsed_response['RESPMSG'];
                $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
            }
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_order_HTML($order_id) {
        try {
            $HTML_STRING = "";
            if (isset($order_id) && !empty($order_id)) {
                $symbol = $this->PSC_Common_Function_OBJ->get_psc_currency_symbol($this->currency);
                $HTML_STRING = apply_filters('pal_shopping_cart_order', sprintf('<ul class="order_details"><li class="order">%s :<strong>%s</strong></li><li class="date">%s :<strong>%s</strong></li><li class="total">%s :<strong><span class="amount"><span class="currencysymbol">%s</span>%s</span></strong></li><li class="method">%s :<strong>%s</strong></li></ul>', esc_html('Order Number'), esc_attr($order_id), esc_html('Date'), esc_attr(get_post_meta($order_id, '_orderdate', true)), esc_html('Total'), esc_attr($symbol), esc_attr(number_format(get_post_meta($order_id, '_psc_cart_total', true), 2, '.', '')), esc_html('Payment Method'), esc_html('PayPal Advanced')));
            }
            return $HTML_STRING;
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_receipt_page($posted) {

        //get the tokens
        $this->secure_token_id = get_post_meta($posted['order-pay'], '_secure_token_id', true);
        $this->securetoken = get_post_meta($posted['order-pay'], '_secure_token', true);

        //display the form in IFRAME, if it is layout C, otherwise redirect to paypal site
        if ($this->layout == 'MINLAYOUT' || $this->layout == 'C') {
            $location = 'https://payflowlink.paypal.com?mode=' . $this->is_mode . '&amp;SECURETOKEN=' . $this->securetoken . '&amp;SECURETOKENID=' . $this->secure_token_id;
            ?> 
            <iframe id="pal_for_edd_iframe" src="<?php echo $location; ?>" width="550" height="565" scrolling="no" frameborder="0" border="0" allowtransparency="true"></iframe>
            <?php
        } else {
            $location = 'https://payflowlink.paypal.com?mode=' . $this->is_mode . '&SECURETOKEN=' . $this->securetoken . '&SECURETOKENID=' . $this->secure_token_id;
            wp_redirect($location);
            exit;
        }
    }

    public function pal_shopping_cart_paypal_advanced_relay_response() {
        try {
            //define a variable to indicate whether it is a silent post or return
            if (isset($_REQUEST['silent']) && $_REQUEST['silent'] == 'true')
                $silent_post = true;
            else
                $silent_post = false;

            //if valid request
            if (!isset($_REQUEST['INVOICE'])) { // Redirect to homepage, if any invalid request or hack
                if ($silent_post === false) {
                    $URL = $this->cart_page;
                    $message = 'if any invalid request or hack';
                    $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
                }
            }
            // get Order ID
            $order_id = $_REQUEST['USER1'];
            // Create order object
            $order = get_post($order_id);

            //check for the status of the order, if completed or processing, redirect to thanks page. This case happens when silentpost is on
            $status = ($order->post_status == 'publish') ? 'pending' : $order->post_status;

            if ($status == 'processing' || $status == 'completed') {
                if ($silent_post === false) {
                    $redirect_url = add_query_arg(array('psc_action' => 'order_received', 'order' => get_post_meta($order_id, '_uniq_id_generatore', true)), $this->order_received_URL);
                    $this->pal_shopping_cart_paypal_advanced_redirect_to($redirect_url);
                }
            }

            if (isset($_REQUEST['cancel']) && $_REQUEST['cancel'] == 'true')
                $_REQUEST['RESULT'] = -1;

            //handle the successful transaction
            switch ($_REQUEST['RESULT']) {

                case 0 :
                    //handle exceptional cases
                    if ($_REQUEST['RESPMSG'] == 'Approved')
                        $this->pal_shopping_cart_paypal_advanced_success_handler($order, $order_id, $silent_post);
                    else if ($_REQUEST['RESPMSG'] == 'Declined')
                        $this->pal_shopping_cart_paypal_advanced_decline_handler($order, $order_id, $silent_post);
                    else
                        $this->pal_shopping_cart_paypal_advanced_error_handler($order, $order_id, $silent_post);
                    break;
                case 12:
                    $this->pal_shopping_cart_paypal_advanced_decline_handler($order, $order_id, $silent_post);
                    break;
                case -1:
                    $this->pal_shopping_cart_paypal_advanced_cancel_handler();
                    break;
                default:
                    //handles error order
                    $this->pal_shopping_cart_paypal_advanced_error_handler($order, $order_id, $silent_post);
                    break;
            }            
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_paypal_advanced_success_handler($order, $order_id, $silent_post) {

        if (get_post_meta($order_id, '_secure_token', true) == $_REQUEST['SECURETOKEN']) {
            
        } else {
            //redirect to the checkout page, if not silent post
            if ($silent_post === false) {                
                $this->pal_shopping_cart_paypal_advanced_session_error_notice_display('Securetoken Can\'t Match.');
                $this->pal_shopping_cart_paypal_advanced_redirect_to(get_permalink($this->cart_page));                
            }
        }
        $inq_result = $this->pal_shopping_cart_paypal_advanced_inquiry_transaction($order, $order_id);
        // Handle response
        if ($inq_result == 'Approved') {//if approved
            
            $this->PSC_Common_Function_OBJ->pac_stock_management_store();
            $this->PSC_Common_Function_OBJ->customer_session_empty();
            $this->PSC_Common_Function_OBJ->session_remove('cart_total_discount');
            $this->PSC_Common_Function_OBJ->session_remove('coupon_cart_discount_array');
            if ($silent_post === false) {
               $redirect_url = add_query_arg(array('psc_action' => 'order_received', 'order' => get_post_meta($order_id, '_uniq_id_generatore', true)), $this->order_received_URL);
               $this->pal_shopping_cart_paypal_advanced_redirect_to($redirect_url);
            }
        }        
    }

    public function pal_shopping_cart_paypal_advanced_decline_handler($order, $order_id, $silent_post) {        
        $this->pal_shopping_cart_paypal_advanced_error_handler($order, $order_id, $silent_post);
        return true;
    }

    public function pal_shopping_cart_paypal_advanced_error_handler($order, $order_id, $silent_post) {

        $this->pal_shopping_cart_paypal_advanced_session_error_notice_display($_POST['RESPMSG']);       
        if ($silent_post === false)
            $this->pal_shopping_cart_paypal_advanced_redirect_to($this->cart_page);
    }

    public function pal_shopping_cart_paypal_advanced_cancel_handler() {

        $URL = $this->cart_page;
        $message = 'Order Canceleld';
        $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
    }

    public function pal_shopping_cart_paypal_advanced_inquiry_transaction($order, $order_id) {

        $paypal_args = array(
            'USER' => $this->paypal_user,
            'VENDOR' => $this->paypal_vendor,
            'PARTNER' => $this->paypal_partner,
            'PWD[' . strlen($this->paypal_password) . ']' => $this->paypal_password,
            'ORIGID' => $_POST['PNREF'],
            'TENDER' => 'C',
            'TRXTYPE' => 'I',
            'BUTTONSOURCE' => $this->buttonsource
        );

        $postData = ''; //stores the post data string
        foreach ($paypal_args as $key => $val) {
            $postData .='&' . $key . '=' . $val;
        }

        $postData = trim($postData, '&');

        /* Using Curl post necessary information to the Paypal Site to generate the secured token */
        $response = wp_remote_post($this->Paypal_URL, array(
            'method' => 'POST',
            'body' => $postData,
            'timeout' => 70,
            'user-agent' => 'pal-shopping-cart',
            'httpversion' => '1.1',
            'headers' => array('host' => 'www.paypal.com')
        ));
        if (is_wp_error($response)) {
            $this->pal_shopping_cart_write_activity_log_permition($response->get_error_message());
            $URL = $this->cart_page;
            $message = $response->get_error_message();
            $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
        }
        if (empty($response['body'])) {

            $URL = $this->cart_page;
            $message = 'Empty response.';
            $this->pal_shopping_cart_write_activity_log_permition('Empty response.');
            $this->pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message);
        }

        /* Parse and assign to array */
        $inquiry_result_arr = array(); //stores the response in array format
        parse_str($response['body'], $inquiry_result_arr);

        if ($inquiry_result_arr['RESULT'] == 0 && $inquiry_result_arr['RESPMSG'] == 'Approved') {
            $this->pal_shopping_cart_write_activity_log_permition($inquiry_result_arr);
            update_post_meta($order_id, '_psc_cart_paymentstatus', $inquiry_result_arr['RESPMSG']);
            update_post_meta($order_id, '_order_transactionid', $inquiry_result_arr['PNREF']);
            $this->pal_shopping_cart_update_payment_status_by_paypal_responce($inquiry_result_arr);            
            $total_amount_array = array();
            array_push($total_amount_array, array('AMT' => $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_total(), 'CURRENCYCODE' => $this->currency, 'TRANSACTIONID' => $inquiry_result_arr['PNREF']));
            update_post_meta($order_id, '_order_responce', serialize($total_amount_array));            
            return 'Approved';
        } else {
            $this->pal_shopping_cart_write_activity_log_permition($inquiry_result_arr);
            return 'Error';
        }
    }

    public function pal_shopping_cart_paypal_advanced_redirect_to($redirect_url) {
        // Clean
        @ob_clean();

        // Header
        header('HTTP/1.1 200 OK');

        //redirect to the url based on layout type
        if ($this->layout != 'MINLAYOUT') {
            wp_redirect($redirect_url);
        } else {
            echo "<script>window.parent.location.href='" . $redirect_url . "';</script>";
        }
        exit;
    }
   

    public function pal_shopping_cart_paypal_advanced_error_notice_display($URL, $message) {
        try {
            $error_array = array();
            $my_error = array('L_LONGMESSAGE' => $message);
            array_push($error_array, $my_error);
            $this->PSC_Common_Function_OBJ->session_set('PSC_PAYMENT_ERROR', $error_array);
            wp_redirect($URL);
            exit;
        } catch (Exception $ex) {
            
        }
    }
    
    public function pal_shopping_cart_paypal_advanced_session_error_notice_display($message) {
        try {
            $error_array = array();
            $my_error = array('L_LONGMESSAGE' => $message);
            array_push($error_array, $my_error);
            $this->PSC_Common_Function_OBJ->session_set('PSC_PAYMENT_ERROR', $error_array);
            return TRUE;
        } catch (Exception $ex) {
            
        }
    }

    public function pal_shopping_cart_write_activity_log_permition($data) {

        if (!$this->debug) {
            return;
        }
        $paypallog = new Paypal_Shopping_Cart_Logger();
        $paypallog->add('paypal_shopping_cart_log', 'PayPal Advanced ' . print_r($data, true));
        return;
    }
    
    public function pal_shopping_cart_update_payment_status_by_paypal_responce($result) {

        switch ($result['RESULT']) :
            case '0' :
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('Payment Completed via PayPal Advanced');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'completed'));
                break;
            case '126' :
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('The transaction has been authorized but requires you to review and to manually accept the transaction before it will be allowed to settle.');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'completed'));
                break;
            case '127' :
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('The transaction has been authorized but requires you to review and to manually accept the transaction before it will be allowed to settle.');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'completed'));
                break;
            default:
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('The transaction has been pending.');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'pending'));
                break;
        endswitch;
        return;
    }

}
