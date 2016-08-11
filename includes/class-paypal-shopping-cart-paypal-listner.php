<?php

/**
 * @class       Pal_Shopping_Cart_PayPal_listner
 * @version	1.0.0
 * @package	pal-shopping-cart
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class PayPal_Shopping_Cart_PayPal_listner {

    public function __construct() {

        $this->liveurl = 'https://ipnpb.paypal.com/cgi-bin/webscr';
        $this->testurl = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    }

    public function check_ipn_request() {
        @ob_clean();
        $ipn_response = !empty($_POST) ? $_POST : false;
        if ($ipn_response && $this->check_ipn_request_is_valid($ipn_response)) {
            header('HTTP/1.1 200 OK');
            return true;
        } else {
            return false;
        }
    }

    public function check_ipn_request_is_valid($ipn_response) {
        $is_sandbox = (isset($ipn_response['test_ipn'])) ? 'yes' : 'no';
        if ('yes' == $is_sandbox) {
            $paypal_adr = $this->testurl;
        } else {
            $paypal_adr = $this->liveurl;
        }
        $validate_ipn = array('cmd' => '_notify-validate');
        $validate_ipn += stripslashes_deep($ipn_response);
        $params = array(
            'body' => $validate_ipn,
            'sslverify' => false,
            'timeout' => 60,
            'httpversion' => '1.1',
            'compress' => false,
            'decompress' => false,
            'user-agent' => 'pal-shopping-cart/'
        );
        $response = wp_remote_post($paypal_adr, $params);
        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
            return true;
        }
        return false;
    }

    public function successful_request($IPN_status) {
        $ipn_response = !empty($_POST) ? $_POST : false;
        $ipn_response['IPN_status'] = ( $IPN_status == true ) ? 'Verified' : 'Invalid';
        $posted = stripslashes_deep($ipn_response);
        $this->ipn_response_data_handler($posted);
    }

    public function ipn_response_data_handler($posted = null) {
        
        $log = new Paypal_Shopping_Cart_Logger();
        $log->add('paypal_shopping_cart_callback', print_r($posted, true));
        
        if (isset($posted) && !empty($posted)) {            
            if (isset($posted['parent_txn_id']) && !empty($posted['parent_txn_id'])) {                                
                
                if (!class_exists('PayPal_Express_PayPal')) {
                    require_once( PAYPAL_FOR_PAYPAL_SHOPPING_CART_PLUGIN_DIR . '/public/partials/lib/paypal.class.php' );                    
                }

                if (isset($posted['auth_id']) && !empty($posted['auth_id'])) {
                    $paypal_txn_id = $posted['auth_id'];
                } else {
                    $paypal_txn_id = $posted['parent_txn_id'];
                }

                $PayPalConfig = array();
                $is_post_id = $this->paypal_shopping_cart_exist_post_by_title($paypal_txn_id);
                if (isset($is_post_id) && !empty($is_post_id)) {
                    $PayPalConfig = $this->pal_shopping_cart_check_method_get_tranzaction($is_post_id);                    
                }       
                $PayPal = new PayPal_Express_PayPal($PayPalConfig);
                $GTDFields = array('transactionid' => $posted['parent_txn_id']);                
                $PayPalRequestData = array('GTDFields' => $GTDFields);
                $PayPalResult = $PayPal->GetTransactionDetails($PayPalRequestData);      

                $posted['payment_status'] = isset($PayPalResult['PAYMENTSTATUS'])?$PayPalResult['PAYMENTSTATUS']:'';                                  
            } else if (isset($posted['txn_id'])) {
                $paypal_txn_id = $posted['txn_id'];
            } else {
                return false;
            }
            if ($this->paypal_shopping_cart_exist_post_by_title($paypal_txn_id) != false) {                
                $post_id = $this->paypal_shopping_cart_exist_post_by_title($paypal_txn_id);                
                wp_update_post(array('ID' => $post_id, 'post_status' => $posted['payment_status']));
               // $log->add('paypal_shopping_cart_update_order_status', 'Order Id: ' . $post_id . ' ' . ' Transaction Id: ' . $paypal_txn_id . ' ' . 'Status: ' . $posted['payment_status']);
                $order_note = 'Order Id: ' . $post_id . ' ' . ' Transaction Id: ' . $paypal_txn_id . ' ' . 'Status: ' . $posted['payment_status'];
                $this->update_payment_status_by_paypal_responce_comment_table($post_id, $order_note);                
            }
        }
    }

    function update_payment_status_by_paypal_responce_comment_table($post_id, $order_note) {

        $time = current_time('mysql');
        $current_user = wp_get_current_user();
        $result = array(
            'comment_post_ID' => $post_id,
            'comment_author' => isset($current_user->user_login) ? $current_user->user_login : '',
            'comment_author_email' => isset($current_user->user_email) ? $current_user->user_email : '',
            'comment_author_url' => '',
            'comment_author_IP' => '',
            'comment_date' => $time,
            'comment_date_gmt' => $time,
            'comment_content' => $order_note,
            'comment_karma' => 0,
            'comment_approved' => 0,
            'comment_agent' => 'Pal-Shopping-Cart',
            'comment_type' => 'order_note',
            'comment_parent' => 0,
            'user_id' => 0
        );
        wp_insert_comment($result);
        return;
    }

    function paypal_shopping_cart_exist_post_by_title($ipn_txn_id) {
        global $wpdb;
        $post_data = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->postmeta}, {$wpdb->posts} WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_value = %s AND $wpdb->postmeta.meta_key = '_order_transactionid' AND $wpdb->posts.post_type = 'psc_order' ", $ipn_txn_id));      
        if (empty($post_data)) {
            return false;
        } else {
            return $post_data;
        }
    }

    function pal_shopping_cart_check_method_get_tranzaction($post_id) {
        $PayPalConfig = array();        

        $payment_method = get_post_meta($post_id,'_payment_method',true);                
        if (isset($payment_method) && strtolower($payment_method) == 'paypal_express') {            
            $is_sand_box = (get_option('psc_pec_testmode') == 'yes') ? TRUE : FALSE;            
            if ($is_sand_box) {
                $PayPalConfig = array(
                    'Sandbox' => $is_sand_box,
                    'APIUsername' => (get_option('psc_pec_sandbox_api_username')) ? get_option('psc_pec_sandbox_api_username') : '',
                    'APIPassword' => (get_option('psc_pec_sandbox_api_password')) ? get_option('psc_pec_sandbox_api_password') : '',
                    'APISignature' => (get_option('psc_pec_sandbox_api_signature')) ? get_option('psc_pec_sandbox_api_signature') : '',
                    'PrintHeaders' => '',
                    'LogResults' => '',
                    'LogPath' => '',
                );
            } else {
                $PayPalConfig = array(
                    'Sandbox' => $is_sand_box,
                    'APIUsername' => (get_option('psc_pec_api_username')) ? get_option('psc_pec_api_username') : '',
                    'APIPassword' => (get_option('psc_pec_api_password')) ? get_option('psc_pec_api_password') : '',
                    'APISignature' => (get_option('psc_pec_api_signature')) ? get_option('psc_pec_api_signature') : '',
                    'PrintHeaders' => '',
                    'LogResults' => '',
                    'LogPath' => '',
                );
            }            
        } else if (isset($payment_method) && strtolower($payment_method) == 'paypal_pro') {            
            $is_sand_box = (get_option('psc_pec_paypal_pro_testmode') == 'yes') ? TRUE : FALSE;            
            if ($is_sand_box) {
                $PayPalConfig = array(
                    'Sandbox' => $is_sand_box,
                    'APIUsername' => (get_option('psc_pec_paypal_pro_sandbox_api_username')) ? get_option('psc_pec_paypal_pro_sandbox_api_username') : '',
                    'APIPassword' => (get_option('psc_pec_paypal_pro_sandbox_api_password')) ? get_option('psc_pec_paypal_pro_sandbox_api_password') : '',
                    'APISignature' => (get_option('psc_pec_paypal_pro_sandbox_api_signature')) ? get_option('psc_pec_paypal_pro_sandbox_api_signature') : '',
                    'PrintHeaders' => '',
                    'LogResults' => '',
                    'LogPath' => '',
                );
            } else {
                $PayPalConfig = array(
                    'Sandbox' => $is_sand_box,
                    'APIUsername' => (get_option('psc_pec_paypal_pro_live_api_username')) ? get_option('psc_pec_paypal_pro_live_api_username') : '',
                    'APIPassword' => (get_option('psc_pec_paypal_pro_live_api_password')) ? get_option('psc_pec_paypal_pro_live_api_password') : '',
                    'APISignature' => (get_option('psc_pec_paypal_pro_live_api_signature')) ? get_option('psc_pec_paypal_pro_live_api_signature') : '',
                    'PrintHeaders' => '',
                    'LogResults' => '',
                    'LogPath' => '',
                );
            }            
        }
        return $PayPalConfig;
    }

}
