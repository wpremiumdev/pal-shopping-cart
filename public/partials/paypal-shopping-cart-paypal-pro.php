<?php

class Paypal_Shopping_Cart_PayPal_Pro {

    public $PSC_Common_Function_OBJ;

    public $PSC_Common_Cart_Function_OBJ;
    
    public function __construct() {
        $this->id = 'PayPal_Pro';
        $this->api_version = '120';
        $this->title = (get_option('psc_pec_paypal_pro_title')) ? get_option('psc_pec_paypal_pro_title') : '';
        $this->description = (get_option('psc_pec_paypal_pro_description')) ? get_option('psc_pec_paypal_pro_description') : '';
        $this->testmode = (get_option('psc_pec_paypal_pro_testmode') == 'yes') ? TRUE : FALSE;
        $this->debug = (get_option('psc_pec_paypal_pro_debug') == 'yes') ? TRUE : FALSE;
        $this->currency = (get_option('psc_currency_general_settings')) ? get_option('psc_currency_general_settings') : 'USD';
        $this->paymentaction = (get_option('psc_pec_paypal_pro_action')) ? get_option('psc_pec_paypal_pro_action') : '';
        $this->buttonsource = 'mbjtechnolabs_SP';
        $this->Paypal_URL = '';
        $this->Notifyurl = site_url('?Paypal_Shopping_Cart&action=ipn_handler');
        if ($this->testmode) {
            $this->Paypal_URL = "https://api-3t.sandbox.paypal.com/nvp";
            $this->paypal_username = (get_option('psc_pec_paypal_pro_sandbox_api_username')) ? trim(get_option('psc_pec_paypal_pro_sandbox_api_username')) : '';
            $this->paypal_password = (get_option('psc_pec_paypal_pro_sandbox_api_password')) ? trim(get_option('psc_pec_paypal_pro_sandbox_api_password')) : '';
            $this->paypal_signature = (get_option('psc_pec_paypal_pro_sandbox_api_signature')) ? trim(get_option('psc_pec_paypal_pro_sandbox_api_signature')) : '';
        } else {
            $this->Paypal_URL = "https://api-3t.paypal.com/nvp";
            $this->paypal_username = (get_option('psc_pec_paypal_pro_live_api_username')) ? trim(get_option('psc_pec_paypal_pro_live_api_username')) : '';
            $this->paypal_password = (get_option('psc_pec_paypal_pro_live_api_password')) ? trim(get_option('psc_pec_paypal_pro_live_api_password')) : '';
            $this->paypal_signature = (get_option('psc_pec_paypal_pro_live_api_signature')) ? trim(get_option('psc_pec_paypal_pro_live_api_signature')) : '';
        }
        $this->PSC_Common_Function_OBJ = new PSC_Common_Function();
        $this->PSC_Common_Cart_Function_OBJ = new PSC_Common_Cart_Function();
        
    }

    public function pay_shopping_cart_pro_process_payment($posted) {
        try {
            if (is_array($posted) && count($posted) > 0) {
                $posted_data = $this->pay_shopping_cart_pro_postdata($posted);
                $response = $this->pay_shopping_cart_request_remote($posted_data);
                if (is_wp_error($response)) {
                    $this->pay_shopping_cart_write_activity_log_permition($response->get_error_message());
                    $url = get_permalink($this->PSC_Common_Function_OBJ->psc_cart_page());
                    $this->pay_shopping_cart_after_process_redirect_page($url);
                }
                parse_str($response['body'], $parsed_response);
                if ($parsed_response['ACK'] == 'SuccessWithWarning' || $parsed_response['ACK'] == 'Success') {
                    $this->pay_shopping_cart_write_activity_log_permition($parsed_response);
                    $uniq_id_generatore = $this->pay_shopping_cart_update_post_data($parsed_response, $posted);
                    $this->pay_shopping_cart_update_payment_status_by_paypal_responce($parsed_response);
                    $this->pay_shopping_cart_send_email_notification_user_and_admin($parsed_response, $posted);
                    $this->PSC_Common_Function_OBJ->pac_stock_management_store();
                    $this->PSC_Common_Function_OBJ->customer_session_empty();
                    $this->PSC_Common_Function_OBJ->session_remove('cart_total_discount');
                    $this->PSC_Common_Function_OBJ->session_remove('coupon_cart_discount_array');
                    $psc_order_id = $this->PSC_Common_Function_OBJ->psc_get_page_id_by_title('Order Received');
                    if (isset($psc_order_id) && $psc_order_id != null) {
                        $order_page_url = get_permalink($psc_order_id->ID);
                    }
                    $url = add_query_arg(array('psc_action' => 'order_received', 'order' => $uniq_id_generatore), $order_page_url);
                    $this->pay_shopping_cart_after_process_redirect_page($url);
                } else {

                    $this->pay_shopping_cart_write_activity_log_permition($parsed_response);
                    $error_array = array();
                    $my_error = array('L_LONGMESSAGE' => $parsed_response['L_LONGMESSAGE0']);
                    array_push($error_array, $my_error);
                    $this->PSC_Common_Function_OBJ->session_set('PSC_PAYMENT_ERROR', $error_array);
                    $url = get_permalink($this->PSC_Common_Function_OBJ->psc_cart_page());
                    $this->pay_shopping_cart_after_process_redirect_page($url);
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    public function pay_shopping_cart_pro_postdata($posted) {
        try {
            $post_data = array();
            $post_data['VERSION'] = '121';
            $post_data['SIGNATURE'] = $this->paypal_signature;
            $post_data['USER'] = $this->paypal_username;
            $post_data['PWD'] = $this->paypal_password;
            $post_data['METHOD'] = 'DoDirectPayment';
            $post_data['PAYMENTACTION'] = $this->paymentaction;
            $post_data['IPADDRESS'] = $this->pay_shopping_cart_user_ip();
            $post_data['AMT'] = $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_total();
            $post_data['INVNUM'] = substr(microtime(), -5);
            $post_data['CURRENCYCODE'] = $this->currency;
            $post_data['CREDITCARDTYPE'] = '';
            $post_data['ACCT'] = $posted['pro_card_number'];
            $post_data['EXPDATE'] = $posted['pro_card_ex_mm'] . '' . $posted['pro_card_ex_yy'];
            $post_data['STARTDATE'] = '';
            $post_data['CVV2'] = $posted['pro_card_cvc'];
            $post_data['EMAIL'] = $posted['billing_email'];
            $post_data['FIRSTNAME'] = $posted['pro_card_holder_name'];
            $post_data['DESC'] = $this->description;
            $post_data['NOTIFYURL'] = $this->Notifyurl;
            $post_data['BUTTONSOURCE'] = $this->buttonsource;

            return $post_data;
        } catch (Exception $Ex) {
            
        }
    }

    public function pay_shopping_cart_update_post_data($parsed_response, $posted) {
        try {

            $order_id = $this->PSC_Common_Function_OBJ->create_order();
            $chosen_shipping_methods = 'PayPal Pro';
            $this->PSC_Common_Function_OBJ->session_set('chosen_shipping_methods', $chosen_shipping_methods);
            $this->PSC_Common_Function_OBJ->session_set('order_id', $order_id);
            update_post_meta($order_id, '_payment_method_title', $this->title);
            update_post_meta($order_id, '_payment_method', $this->id);
            if (is_user_logged_in()) {
                $userLogined = wp_get_current_user();
                update_post_meta($order_id, '_billing_email', $userLogined->user_email);
            } else {
                update_post_meta($order_id, '_billing_email', $posted['billing_email']);
            }

            update_post_meta($order_id, '_shipping_first_name', $posted['billing_first_name']);
            update_post_meta($order_id, '_shipping_last_name', $posted['billing_last_name']);
            update_post_meta($order_id, '_shipping_full_name', $posted['billing_first_name'] . ' ' . $posted['billing_last_name']);
            update_post_meta($order_id, '_shipping_company', $posted['billing_company']);
            update_post_meta($order_id, '_billing_phone', $posted['billing_phone']);
            update_post_meta($order_id, '_shipping_address_1', $posted['billing_address_1']);
            update_post_meta($order_id, '_shipping_address_2', $posted['billing_address_2']);
            update_post_meta($order_id, '_shipping_city', $posted['billing_city']);
            update_post_meta($order_id, '_shipping_postcode', $posted['billing_postcode']);
            update_post_meta($order_id, '_shipping_country', $posted['billing_country']);
            update_post_meta($order_id, '_shipping_state', $posted['billing_state']);
            update_post_meta($order_id, '_customer_user', get_current_user_id());
            $uniq_id_generatore = $order_id . '' . substr(microtime(), -10);
            $psc_cart_serialize = serialize($this->PSC_Common_Function_OBJ->session_cart_contents());
            update_post_meta($order_id, '_uniq_id_generatore', $uniq_id_generatore);
            update_post_meta($order_id, '_psc_cart_subtotal', $this->PSC_Common_Function_OBJ->psc_calculate_cart_totals());
            update_post_meta($order_id, '_order_cart_discount', serialize($this->PSC_Common_Function_OBJ->get_coupon_discount_and_code()));
            update_post_meta($order_id, '_psc_cart_total', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_total());
            update_post_meta($order_id, '_psc_cart_paymentstatus', $parsed_response['TRANSACTIONID']);
            update_post_meta($order_id, '_order_transactionid', $parsed_response['TRANSACTIONID']);
            update_post_meta($order_id, '_psc_cart_serialize', $psc_cart_serialize);
            update_post_meta($order_id, '_order_responce_total_tax', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_tax_total());
            update_post_meta($order_id, '_order_responce_total_shipping', $this->PSC_Common_Cart_Function_OBJ->psc_get_cart_shipping_total());
            $total_amount_array = array();
            array_push($total_amount_array, $parsed_response);
            update_post_meta($order_id, '_order_responce', serialize($total_amount_array));

            return $uniq_id_generatore;
        } catch (Exception $ex) {
            
        }
    }

    public function pay_shopping_cart_update_payment_status_by_paypal_responce($result) {

        switch (strtolower($result['ACK'])) :
            case 'success' :
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('Payment Completed via PayPal Pro');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'completed'));
                break;
            case 'successwithwarning' :
                $this->PSC_Common_Function_OBJ->update_payment_status_by_paypal_responce_comment_table('The request was successful but a warning error code was also returned.');
                wp_update_post(array('ID' => $this->PSC_Common_Function_OBJ->session_get('order_id'), 'post_status' => 'completed'));
                break;
            default:
                break;
        endswitch;
        return;
    }

    public function pay_shopping_cart_user_ip() {

        return !empty($_SERVER['HTTP_X_FORWARD_FOR']) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

     public function pay_shopping_cart_request_remote($posted) {
        try {
            return wp_remote_post($this->Paypal_URL, array('method' => 'POST', 'headers' => array('PAYPAL-NVP' => 'Y'), 'body' => $posted, 'timeout' => 70, 'user-agent' => 'Pro', 'httpversion' => '1.1'));
        } catch (Exception $ex) {
            
        }
    }

    public function pay_shopping_cart_after_process_redirect_page($url) {
        wp_redirect($url);
        exit;
    }

    public function pay_shopping_cart_write_activity_log_permition($data) {

        if (!$this->debug) {
            return;
        }
        $paypallog = new Paypal_Shopping_Cart_Logger();
        $paypallog->add('paypal_shopping_cart_log', 'PayPal Pro ' . print_r($data, true));
        return;
    }

    public function pay_shopping_cart_send_email_notification_user_and_admin($PayPalResult, $posted) {

        $psc_donor_notification = (get_option('psc_donor_notification')) ? get_option('psc_donor_notification') : 'yes';
        $psc_admin_notification = (get_option('psc_admin_notification')) ? get_option('psc_admin_notification') : 'yes';

        if (( isset($psc_donor_notification) && 'yes' == $psc_donor_notification ) || (isset($psc_admin_notification) && 'yes' == $psc_admin_notification)) {            
            $posted_result_array = $this->PSC_Common_Function_OBJ->get_post_meta_all($this->PSC_Common_Function_OBJ->session_get('order_id'));
            $posted_result_email['payeremail'] = $posted['billing_email'];
            $posted_result_email['payment_date'] = current_time('mysql');
            $posted_result_email['txn_id'] = $posted_result_array['_order_transactionid'];
            $posted_result_email['USER'] = $this->paypal_username;            
            $posted_result_email['PAYMENTREQUEST_0_SHIPTONAME'] = $posted_result_array['_shipping_full_name'];
            $posted_result_email['PAYMENTREQUEST_0_CURRENCYCODE'] = $this->currency;
            $posted_result_email['PAYMENTREQUEST_0_AMT'] = $posted_result_array['_psc_cart_total'];
            do_action('psc_send_notification_mail', $posted_result_email);
        }
    }

}
