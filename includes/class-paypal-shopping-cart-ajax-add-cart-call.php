<?php

if (!defined('ABSPATH')) {
    exit;
}

class PSC_AJAX_Handler {

    public static function init() {
        add_action('wp_ajax_psc_add_to_cart_item', array(__CLASS__, 'psc_add_to_cart_item'));
        add_action('wp_ajax_nopriv_psc_add_to_cart_item', array(__CLASS__, 'psc_add_to_cart_item'));
        add_action('wp_ajax_psc_update_cart_item', array(__CLASS__, 'psc_update_cart_item'));
        add_action('wp_ajax_nopriv_psc_update_cart_item', array(__CLASS__, 'psc_update_cart_item'));
        add_action('wp_ajax_psc_update_country_state', array(__CLASS__, 'psc_update_country_state'));
        add_action('wp_ajax_nopriv_psc_update_country_state', array(__CLASS__, 'psc_update_country_state'));
        add_action('wp_ajax_psc_update_cart_with_coupon', array(__CLASS__, 'psc_update_cart_with_coupon'));
        add_action('wp_ajax_nopriv_psc_update_cart_with_coupon', array(__CLASS__, 'psc_update_cart_with_coupon'));
        add_action('wp_ajax_psc_select_variable_product', array(__CLASS__, 'psc_select_variable_product'));
        add_action('wp_ajax_nopriv_psc_select_variable_product', array(__CLASS__, 'psc_select_variable_product'));
        add_action('wp_ajax_set_update_cart_session_result', array(__CLASS__, 'set_update_cart_session_result'));
        add_action('wp_ajax_nopriv_set_update_cart_session_result', array(__CLASS__, 'set_update_cart_session_result'));
        add_action('wp_ajax_psc_add_order_note', array(__CLASS__, 'psc_add_order_note'));
        add_action('wp_ajax_nopriv_psc_add_order_note', array(__CLASS__, 'psc_add_order_note'));
        add_action('wp_ajax_psc_delete_order_note', array(__CLASS__, 'psc_delete_order_note'));
        add_action('wp_ajax_nopriv_psc_delete_order_note', array(__CLASS__, 'psc_delete_order_note'));
    }

    public static function psc_add_to_cart_item() {

        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $psc_product_id = !empty($_POST['value']) ? $_POST['value'] : false;
        $PSC_Common_Function = new PSC_Common_Function();
        $cart_item_obj = new PSC_Cart_Handler();

        $PID = isset($psc_product_id[0]) ? sanitize_text_field($psc_product_id[0]) : '';
        $PACTION = isset($psc_product_id[1]) ? sanitize_text_field($psc_product_id[1]) : '';
        $PQTY = isset($psc_product_id[2]) ? intval($psc_product_id[2]) : '';
        $PNAME = isset($psc_product_id[3]) ? sanitize_text_field(strip_slashes($psc_product_id[3])) : '';
        $is_stock_is = '';
        if (is_array($psc_product_id) && "insert" == $PACTION) {
            $is_stock_is = self::is_stock_available_current_product_id($PID, $PNAME);
            if (isset($PNAME) && !empty($PNAME)) {
                self::is_variations_product_update_OR_insert($PSC_Common_Function, $cart_item_obj, $is_stock_is, $PID, $PQTY, $PNAME);
            } else {
                self::is_simple_product_update_OR_insert($PSC_Common_Function, $cart_item_obj, $is_stock_is, $PID, $PQTY, $PNAME);
            }
        } elseif (is_array($psc_product_id) && "delete" == $PACTION) {
            self::is_product_cart_item_delete($cart_item_obj, $PID);
        }
        echo esc_html('success');
        die();
    }

    public static function is_variations_product_update_OR_insert($PSC_Common_Function, $cart_item_obj, $is_stock_is, $PID, $PQTY, $PNAME) {

        try {
            $result_product = $PSC_Common_Function->get_post_meta_all($PID);
            $result_unsirealize = $PSC_Common_Function->get_unserialize_data($result_product['psc_variable_product_data']);
            if ('instock' != $is_stock_is && $is_stock_is < $PQTY) {
                $PSC_Common_Function->session_set('add_to_cart_stock_is_big_item', 'item');
                $PSC_Common_Function->session_set('add_to_cart_stock_is_item_qty_add', $is_stock_is);
                $url = get_permalink($PID);
                $return_array = array(
                    'MSG' => 'stock_empty',
                    'URL' => $url
                );
                echo json_encode($return_array);
                die();
            }

            foreach ($result_unsirealize as $key => $value) {
                if ($PNAME == $value['psc_variable_product_name' . $key]) {
                    $md5_id = md5($PID . '_' . $PNAME);
                    $is_exist = self::is_product_rowid_exist($PSC_Common_Function, $md5_id);
                    $is_md5_id_exist = isset($is_exist[0]) ? $is_exist[0] : '';
                    $is_qty = isset($is_exist[1]) ? $is_exist[1] : '';
                    if ((isset($is_stock_is) && $is_stock_is > 0) || (isset($is_stock_is) && $is_stock_is == 'instock')) {
                        if ($is_qty >= $is_stock_is) {
                            $PSC_Common_Function->session_set('add_to_cart_stock_is_empty', 'empty');
                            $PSC_Common_Function->session_set('add_to_cart_stock_is_qty', $is_stock_is);
                            $url = get_permalink($PID);
                            $return_array = array(
                                'MSG' => 'stock_empty',
                                'URL' => $url
                            );
                            echo json_encode($return_array);
                            die();
                        }
                        if ($is_md5_id_exist == true) {
                            self::is_update_product_cart($cart_item_obj, $md5_id, $is_qty);
                        } else {

                            self::is_insert_variations_product_cart($cart_item_obj, $PID, $PNAME, $PQTY, $key, $value, $result_product);
                        }
                    }
                }
            }
            return;
        } catch (Exception $ex) {
            
        }
    }

    public static function is_simple_product_update_OR_insert($PSC_Common_Function, $cart_item_obj, $is_stock_is, $PID, $PQTY, $PNAME) {

        try {
            if ('instock' != $is_stock_is && $is_stock_is < $PQTY) {
                $PSC_Common_Function->session_set('add_to_cart_stock_is_big_item', 'item');
                $PSC_Common_Function->session_set('add_to_cart_stock_is_item_qty_add', $is_stock_is);
                $url = get_permalink($PID);
                $return_array = array(
                    'MSG' => 'stock_empty',
                    'URL' => $url
                );
                echo json_encode($return_array);
                die();
            }

            $result_product = $PSC_Common_Function->get_post_meta_all($PID);
            $md5_id = md5($PID);
            $is_exist = self::is_product_rowid_exist($PSC_Common_Function, $md5_id);
            $is_md5_id_exist = isset($is_exist[0]) ? $is_exist[0] : '';
            $is_qty = isset($is_exist[1]) ? $is_exist[1] : '';
            if ((isset($is_stock_is) && $is_stock_is > 0) || (isset($is_stock_is) && $is_stock_is == 'instock')) {
                if ($is_qty >= $is_stock_is) {
                    $PSC_Common_Function->session_set('add_to_cart_stock_is_empty', 'empty');
                    $PSC_Common_Function->session_set('add_to_cart_stock_is_qty', $is_stock_is);
                    $url = get_permalink($PID);
                    $return_array = array(
                        'MSG' => 'stock_empty',
                        'URL' => $url
                    );
                    echo json_encode($return_array);
                    die();
                }
                if ($is_md5_id_exist == true) {
                    self::is_update_product_cart($cart_item_obj, $md5_id, $is_qty);
                } else {
                    self::is_insert_simple_product_cart($cart_item_obj, $PID, $PQTY, $result_product);
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    public static function is_product_rowid_exist($PSC_Common_Function, $md5_id) {
        try {
            $result = array();
            $result[0] = false;
            $Get_Cart_Result = $PSC_Common_Function->session_cart_contents();
            foreach ($Get_Cart_Result as $key => $value) {
                if ($md5_id == $key) {
                    $result[0] = true;
                    $result[1] = $value['qty'];
                }
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function is_stock_available_current_product_id($post_id, $productname) {

        try {
            $result = 0;
            $PSC_Common_Function = new PSC_Common_Function();
            $result = $PSC_Common_Function->get_update_stock_by_post_id($post_id, $productname);
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function is_update_product_cart($cart_item_obj, $md5_id, $is_qty) {

        try {
            $data = array('rowid' => $md5_id, 'qty' => $is_qty + 1);
            $cart_item_obj->update($data);
            return;
        } catch (Exception $ex) {
            
        }
    }

    public static function is_insert_variations_product_cart($cart_item_obj, $PID, $PNAME, $PQTY, $key, $value, $result_product) {

        try {            
            $insert_data = array(
                'id' => $PID . '_' . $PNAME,
                'name' => self::get_product_name_by_post_id($PID) . ':' . $PNAME,
                'price' => self::get_price_insert_product_in_cart($key, $value),
                'tax' => self::variation_get_product_tax_shipping_calculate($PID,$key, $value,'tax'),
                'shipping' => self::variation_get_product_tax_shipping_calculate($PID,$key, $value,'ship'),
                'qty' => $PQTY
            );
            $cart_item_obj->insert($insert_data);
            return;
        } catch (Exception $ex) {
            
        }
    }

    public static function get_price_insert_product_in_cart($key, $value) {
        $result = "";
        if (isset($value['psc_variable_product_sale_price' . $key]) && !empty($value['psc_variable_product_sale_price' . $key])) {
            $result = $value['psc_variable_product_sale_price' . $key];
        } else if (isset($value['psc_variable_product_regular_price' . $key]) && !empty($value['psc_variable_product_regular_price' . $key])) {
            $result = $value['psc_variable_product_regular_price' . $key];
        }
        return $result;
    }

    public static function is_insert_simple_product_cart($cart_item_obj, $PID, $PQTY, $result_product) {
        try {           
            $insert_data = array(
                'id' => $PID,
                'name' => self::get_product_name_by_post_id($PID),
                'price' => self::get_simple_product_cart_price($result_product),
                'tax' => self::simple_get_product_tax_shipping_calculate($PID, $result_product, 'tax'),
                'shipping' => self::simple_get_product_tax_shipping_calculate($PID, $result_product, 'ship'),
                'qty' => $PQTY
            );
            $cart_item_obj->insert($insert_data);
            return;
        } catch (Exception $ex) {
            
        }
    }

    public static function simple_get_product_tax_shipping_calculate($PID, $result_product, $name) {
        try {
            $result = 0;
            $tax_ship = 0;
            if( $name == 'tax' ){                
                $tax_ship = get_post_meta( $PID, '_psc_sale_tax', TRUE );
            }            
            if( $name == 'ship' ){                 
                 return (get_post_meta( $PID, '_psc_sale_ship', TRUE ))? get_post_meta( $PID, '_psc_sale_ship', TRUE ) : 0;
            }
            
            $amount = self::get_simple_product_cart_price($result_product);
            
            if (!empty($amount)) {
                $result = number_format($amount * $tax_ship / 100, 2);
            }
            
            return $result;
        } catch (Exception $ex) {
            
        }
    }
    
    public static function variation_get_product_tax_shipping_calculate($PID, $key, $value, $name) {
        try {
            $result = 0;
            $tax_ship = 0;
            if( $name == 'tax' ){                
                $tax_ship = $value['psc_variable_product_tax'.$key];
            }            
            if( $name == 'ship' ){                 
                 return ($value['psc_variable_product_ship'.$key])? $value['psc_variable_product_ship'.$key] : 0;
            }
            
            $amount = ($value['psc_variable_product_sale_price'.$key])?$value['psc_variable_product_sale_price'.$key]:$value['psc_variable_product_regular_price'.$key];
            
            if (!empty($amount)) {
                $result = number_format($amount * $tax_ship / 100, 2);
            }
            
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function get_product_name_by_post_id($post_id){
        try{
            $result = "";            
            $product_parant_post = get_post($post_id);
            $result = isset($product_parant_post->post_title) ? $product_parant_post->post_title : '';
            if (empty($result)) {
                $result = isset($product_parant_post->post_name) ? $product_parant_post->post_name : '';
            }
            return $result;           
        } catch (Exception $ex) {
            
        }
    }

    public static function get_simple_product_cart_price($result_product) {
        $result = "";
        if (isset($result_product['_psc_sale_price']) && !empty($result_product['_psc_sale_price'])) {
            $result = $result_product['_psc_sale_price'];
        } else if (isset($result_product['_psc_regular_price']) && !empty($result_product['_psc_regular_price'])) {
            $result = $result_product['_psc_regular_price'];
        }
        return $result;
    }

    public static function is_product_cart_item_delete($cart_item_obj, $PID) {
        try {
            $data = array(
                'rowid' => $PID,
                'qty' => 0
            );
            $cart_item_obj->update($data);
            return;
        } catch (Exception $ex) {
            
        }
    }

    public static function psc_update_cart_item() {
        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $psc_product_id = !empty($_POST['value']) ? $_POST['value'] : false;
        $update_cart_result = false;
        if (is_array($psc_product_id)) {
            foreach ($psc_product_id as $value) {
                $is_stock_is = self::is_stock_available_current_product_id(intval($value['id']), sanitize_text_field($value['name']));
                if ((isset($is_stock_is) && $is_stock_is > 0) || (isset($is_stock_is) && $is_stock_is == 'instock')) {
                    $remove_to_cart_item = new PSC_Cart_Handler();
                    $remove_to_cart_item->update($value);
                }
            }
        }
        echo esc_html($update_cart_result);
        die();
    }

    public static function set_update_cart_session_result() {
        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $psc_product_id = !empty($_POST['value']) ? $_POST['value'] : false;
        $psc_product_id = sanitize_text_field($psc_product_id);
        $PSC_Common_Function = new PSC_Common_Function();
        $PSC_Common_Function->session_set('update_cart_message', 'success');
        echo esc_html('success');
        die();
    }

    public static function psc_update_country_state() {
        global $states;
        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $code = !empty($_POST['value']) ? $_POST['value'] : false;
        if (file_exists(plugin_dir_path(dirname(__FILE__)) . 'templates/countries/states/' . sanitize_text_field($code[1]) . '.php')) {
            require plugin_dir_path(dirname(__FILE__)) . 'templates/countries/states/' . sanitize_text_field($code[1]) . '.php';
            $result = self::countries_state_option($states, sanitize_text_field($code[0]));
            echo json_encode($result);
            die();
        } else {
            echo esc_html('nofound');
            die();
        }
    }

    public static function countries_state_option($states, $address_state) {
        try {
            $result = '<select name="' . $address_state . '" id="' . $address_state . '" class="psc-custom-select ' . $address_state . '">';
            foreach ($states as $data) {
                foreach ($data as $key => $value) {
                    $result .= '<option value="' . $key . '" >' . $value . '</option>';
                }
            }
            return $result . '</select>';
        } catch (Exception $ex) {
            
        }
    }

    public static function psc_update_cart_with_coupon() {
        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $coupon_code = !empty($_POST['value']) ? $_POST['value'] : false;
        $inser_main_array = array();
        $result = array();
        $PSC_Common_Function = new PSC_Common_Function();
        $cart_array_get = array();
        $code_is_diff = true;
        $cart_array_get = self::get_cart_session_array($PSC_Common_Function);
        $coupon_code = sanitize_text_field(strip_slashes($coupon_code));
        if (is_array($cart_array_get) && count($cart_array_get) > 0) {
            foreach ($cart_array_get as $key => $value) {
                if (strtolower($value['coupon_code']) == strtolower($coupon_code)) {
                    $PSC_Common_Function->session_set('coupon_cart_discount_msg', 'Coupon "' . $coupon_code . '" already exists!');
                    $code_is_diff = false;
                }
            }
        } else {
            $result = $PSC_Common_Function->get_all_coupons_with_match($coupon_code);
        }

        if ($code_is_diff) {
        $result = $PSC_Common_Function->get_all_coupons_with_match($coupon_code);
        }
        if (isset($result['code_match']) && $result['code_match'] == true) {
            if (is_array($cart_array_get) && count($cart_array_get) > 0) {
                foreach ($cart_array_get as $key => $value) {
                    $inser_main_array[strtolower(str_replace(' ', '_', $value['coupon_code']))] = $value;
                }
            }

            if (is_array($result) && count($result) > 0) {
                if( isset($result['psc_coupon_amount']) && $result['psc_coupon_amount'] > 0 ){
                    $inser_main_array[strtolower(str_replace(' ', '_', $coupon_code))] = self::psc_update_cart_with_coupons($PSC_Common_Function, $result);
                } else {
                    $result['psc_coupon_status'] = $coupon_code." coupon amount 0";
                }
                
            }

            $PSC_Common_Function->session_set('coupon_cart_discount_array', $inser_main_array);
            $PSC_Common_Function->session_set('coupon_cart_discount_msg', $result['psc_coupon_status']);            
            echo esc_html($result['psc_coupon_status']);
            die();
        } elseif (isset($result['code_match']) && $result['code_match'] == false) {
            $PSC_Common_Function->session_set('coupon_cart_discount_msg', $result['psc_coupon_status']);
            echo esc_html($result['psc_coupon_status']);
            die();
        } else {

            echo esc_html('');
            die();
        }
    }

    public static function psc_update_cart_with_coupons($PSC_Common_Function, $result) {

        $psc_card_handler_obj = new PSC_Cart_Handler();

        $psc_coupon_amount = ($result['psc_coupon_amount']) ? $result['psc_coupon_amount'] : 0; //5
        $cart_total_discount = $PSC_Common_Function->session_get('cart_total_discount');  //0
        $cart_total = $psc_card_handler_obj->_cart_contents['cart_total']; //4         

        if (isset($cart_total_discount) && !empty($cart_total_discount)) {
            $cart_total = $cart_total - $cart_total_discount;
            $cart_total = ($cart_total > 0) ? $cart_total : 0;
        }

        if ((isset($cart_total) && $cart_total > 0) && (isset($psc_coupon_amount) && $cart_total > $psc_coupon_amount)) {
            $cart_total_discount = $cart_total_discount + $psc_coupon_amount;
        } else {
            $result['psc_coupon_amount'] = $cart_total;
            $cart_total_discount = $cart_total_discount + $cart_total;
        }

        $PSC_Common_Function->session_set('cart_total_discount', $cart_total_discount);

        return $result;
        }

    public static function get_cart_session_array($PSC_Common_Function) {
        $result = array();
        $result_data = $PSC_Common_Function->session_get('coupon_cart_discount_array');
        if (is_array($result_data) && count($result_data) > 0) {
            $result = $result_data;
        }
        return $result;
    }

    public static function psc_select_variable_product() {
        check_ajax_referer('paypal_shopping_cart_url', 'security');
        $select_array = !empty($_POST['value']) ? $_POST['value'] : false;
        $PSC_Common_Function = new PSC_Common_Function();
        $get_array_result = $PSC_Common_Function->get_post_meta_all(intval($select_array[1]));
        $symbol = $PSC_Common_Function->get_psc_currency_symbol_only();
//        $psc_variable_product_data = unserialize(trim($get_array_result['psc_variable_product_data']));
//        $psc_variable_product_data = unserialize($psc_variable_product_data);
        $psc_variable_product_data = $PSC_Common_Function->get_unserialize_data($get_array_result['psc_variable_product_data']);

        $data = "";
        foreach ($psc_variable_product_data as $key => $value) {
            if (sanitize_text_field(strip_slashes($select_array[0])) == $value['psc_variable_product_name' . $key]) {
                $data['product_details'] = self::get_product_store_details($symbol, $key, $value);
                $data['product_addcart'] = self::get_product_store_status_and_storege($key, $value, intval($select_array[1]), $PSC_Common_Function);
                $data['product_stock'] = self::get_product_store_storege($key, $value, intval($select_array[1]), $PSC_Common_Function);
            }
        }
        echo json_encode($data);
        die();
    }

    public static function get_product_store_details($symbol, $key, $value) {
        $result = "";
        $concte_price_string = "";
        $vs_price = $value['psc_variable_product_sale_price' . $key];
        $vr_price = $value['psc_variable_product_regular_price' . $key];
        $status_class = '';
        $status = '';
        if ((isset($vs_price) && !empty($vs_price) && $vs_price > 0) && isset($vr_price) && !empty($vr_price) && $vr_price > 0) {
            $concte_price_string .= '<del><span class="amount">' . $symbol . number_format($value['psc_variable_product_regular_price' . $key], 2) . '</span></del>&nbsp;&nbsp;&nbsp;';
            $concte_price_string .= '<ins><span class="amount">' . $symbol . number_format($value['psc_variable_product_sale_price' . $key], 2) . '</span></ins>';
        } else if (isset($vs_price) && !empty($vs_price) && $vs_price > 0) {
            $concte_price_string .= '<ins><span class="amount">' . $symbol . number_format($value['psc_variable_product_sale_price' . $key], 2) . '</span></ins>';
        } else if (isset($vr_price) && !empty($vr_price) && $vr_price > 0) {
            $concte_price_string .= '<ins><span class="amount">' . $symbol . number_format($value['psc_variable_product_regular_price' . $key], 2) . '</span></ins>&nbsp;&nbsp;&nbsp;';
        }
        $product_stock = trim($value['psc_variable_product_stock' . $key]);
        if (isset($value['_psc_manage_stock_variable' . $key]) && $value['_psc_manage_stock_variable' . $key] == '1') {
            if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == 1) {
                if (strlen($product_stock) > 0) {
                    if ($product_stock > 0) {
                        $status_class = 'in-stock';
                        $status = $value['psc_variable_product_stock' . $key] . ' In stock';
                    } else {
                        $status_class = 'out-of-stock';
                        $status = 'Out of stock';
                    }
                } else {
                    $status_class = 'in-stock';
                    $status = ' In stock';
                }
            } else if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == 0) {
                $status_class = 'out-of-stock';
                $status = 'Out of stock';
            }
            $result = '<p class="price">' . $concte_price_string . '</p><p class="stock ' . $status_class . '">' . $status . '</p>';
        } else {
            $result = '<p class="price">' . $concte_price_string . '</p>';
        }
        return $result;
    }

    public static function get_product_store_status_and_storege($key, $value, $postid, $PSC_Common_Function) {
        $result = "";
        
        $redirect_page = get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page());
        $redirect_behaviour = get_permalink($PSC_Common_Function->add_cart_after_redirect_behaviour());
        
        if (isset($value['_psc_manage_stock_variable' . $key]) && $value['_psc_manage_stock_variable' . $key] == 1) {
            $Product_stock = trim($value['psc_variable_product_stock' . $key]);
            if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == '1') {
                if (strlen($Product_stock) > 0) {
                    if ($Product_stock > 0) {
                        $result = '<input type="number" name="psc_quantity" id="psc_quantity" value="1" min="1" max="' . $value['psc_variable_product_stock' . $key] . '"><input type="hidden" name="add-to-cart" value="' . $postid . '"><input type="hidden" name="psc_available_stock" id="psc_available_stock" value="' . $value['psc_variable_product_stock' . $key] . '" />';
                        $result .= '<a id="psc_add_to_cart_button' . $postid . '" rel="nofollow" psc-product-id="' . $postid . '" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now' . $postid . '" hidden=""></span></a>';
                        $result .= '<a href="' . $redirect_page . '" class="view_cart' . $postid . ' view_cart" title="View Cart" hidden="">View Cart</a>';
                        $result .= '<input type="hidden" id="add_cart_after_redirect_behaviour" value="' . $redirect_behaviour . '">';
                    } else {
                        $result = '<input type="number" name="psc_quantity" id="psc_quantity" value="0" min="" max="' . $value['psc_variable_product_stock' . $key] . '" disabled><input type="hidden" name="add-to-cart" value="' . $postid . '"><input type="hidden" name="psc_available_stock" id="psc_available_stock" value="' . $value['psc_variable_product_stock' . $key] . '" />';
                        $result .= '<a style="pointer-events: none;cursor: default; background-color:#E8E8E8" id="psc_add_to_cart_button' . $postid . '" rel="nofollow" psc-product-id="' . $postid . '" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now' . $postid . '" hidden=""></span></a>';
                        $result .= '<a href="' . $redirect_page . '" class="view_cart' . $postid . ' view_cart" title="View Cart" hidden="">View Cart</a>';
                        $result .= '<input type="hidden" id="add_cart_after_redirect_behaviour" value="' . $redirect_behaviour . '">';
                    }
                } else {
                    $result = '<input type="number" name="psc_quantity" id="psc_quantity" value="1" min="1" max="' . $value['psc_variable_product_stock' . $key] . '"><input type="hidden" name="add-to-cart" value="' . $postid . '"><input type="hidden" name="psc_available_stock" id="psc_available_stock" value="' . $value['psc_variable_product_stock' . $key] . '" />';
                    $result .= '<a id="psc_add_to_cart_button' . $postid . '" rel="nofollow" psc-product-id="' . $postid . '" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now' . $postid . '" hidden=""></span></a>';
                    $result .= '<a href="' . $redirect_page . '" class="view_cart' . $postid . ' view_cart" title="View Cart" hidden="">View Cart</a>';
                    $result .= '<input type="hidden" id="add_cart_after_redirect_behaviour" value="' . $redirect_behaviour . '">';
                }
            } else if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == 0) {
                $result = '<input type="number" name="psc_quantity" id="psc_quantity" value="0" min="" max="' . $value['psc_variable_product_stock' . $key] . '" disabled><input type="hidden" name="add-to-cart" value="' . $postid . '"><input type="hidden" name="psc_available_stock" id="psc_available_stock" value="' . $value['psc_variable_product_stock' . $key] . '" />';
                $result .= '<a style="pointer-events: none;cursor: default; background-color:#E8E8E8" id="psc_add_to_cart_button' . $postid . '" rel="nofollow" psc-product-id="' . $postid . '" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now' . $postid . '" hidden=""></span></a>';
                $result .= '<a href="' . $redirect_page . '" class="view_cart' . $postid . ' view_cart" title="View Cart" hidden="">View Cart</a>';
                $result .= '<input type="hidden" id="add_cart_after_redirect_behaviour" value="' . $redirect_behaviour . '">';
            }
        } else {
            $result = '<input type="number" name="psc_quantity" id="psc_quantity" min="1" max="" value="1"><input type="hidden" name="add-to-cart" value="' . $postid . '"><input type="hidden" name="psc_available_stock" id="psc_available_stock" value="nolimit" />';
            $result .= '<a id="psc_add_to_cart_button' . $postid . '" rel="nofollow" psc-product-id="' . $postid . '" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now' . $postid . '" hidden=""></span></a>';
            $result .= '<a href="' . $redirect_page . '" class="view_cart' . $postid . ' view_cart" title="View Cart" hidden="">View Cart</a>';
            $result .= '<input type="hidden" id="add_cart_after_redirect_behaviour" value="' . $redirect_behaviour . '">';
        }
        return $result;
    }

    public static function get_product_store_storege($key, $value, $postid, $PSC_Common_Function) {
        $result = true;
        if ($value['_psc_manage_stock_variable' . $key] == 1) {
            $Product_stock = trim($value['psc_variable_product_stock' . $key]);
            if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == '1') {
                if (strlen($Product_stock) > 0) {
                    if ($Product_stock > 0) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                } else {
                    $result = true;
                }
            } else if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == '0') {
                $result = false;
            }
        } else {
            $result = true;
        }
        return $result;
    }

    public static function psc_add_order_note() {
        try {
            check_ajax_referer('paypal_shopping_cart_url', 'security');
            $psc_product_id = !empty($_POST['value']) ? $_POST['value'] : false;

            $current_user = wp_get_current_user();
            $comment_author = '';
            $author = '';
            $time = current_time('mysql');

            if (isset($psc_product_id[2]) && $psc_product_id[2] == 'private') {
                self::is_private_note_data($psc_product_id, $current_user, $comment_author, $author, $time);
            } else {
                self::is_customer_note_data($psc_product_id, $current_user, $comment_author, $author, $time);
            }
        } catch (Exception $ex) {
            
        }
    }

    public static function is_private_note_data($psc_product_id, $current_user, $comment_author, $author, $time) {
        try {
            global $wpdb;
            wp_insert_comment(self::collect_array_data($psc_product_id, $current_user, $comment_author, $author, $time));
            $lastInsertId = $wpdb->insert_id;
            $date_time_value = explode(" ", $time);
            $insert_date = isset($date_time_value[0]) ? $date_time_value[0] : '';
            $insert_time = isset($date_time_value[1]) ? $date_time_value[1] : '';
            $result['type'] = 'private';
            $result['comments'] = $psc_product_id[1];
            $result['comment_ID'] = $lastInsertId;
            $result['date'] = date('F j, Y', strtotime($insert_date));
            $result['time'] = strtolower(date('H:i A', strtotime($insert_time)));
            $result['author'] = $current_user->user_login;
            echo json_encode($result);
            die();
        } catch (Exception $ex) {
            
        }
    }

    public static function is_customer_note_data($psc_product_id, $current_user, $comment_author, $author, $time) {
        try {
            global $wpdb;
            wp_insert_comment(self::collect_array_data($psc_product_id, $current_user, $comment_author, $author, $time));
            $lastInsertId = $wpdb->insert_id;
            add_comment_meta($lastInsertId, 'is_customer_note', '1');
            $date_time_value = explode(" ", $time);
            $insert_date = isset($date_time_value[0]) ? $date_time_value[0] : '';
            $insert_time = isset($date_time_value[1]) ? $date_time_value[1] : '';
            $result['type'] = 'customer';
            $result['comments'] = $psc_product_id[1];
            $result['comment_ID'] = $lastInsertId;
            $result['date'] = date('F j, Y', strtotime($insert_date));
            $result['time'] = strtolower(date('H:i A', strtotime($insert_time)));
            $result['author'] = $current_user->user_login;
            echo json_encode($result);
            die();
        } catch (Exception $ex) {
            
        }
    }

    public static function collect_array_data($psc_product_id, $current_user, $comment_author, $author, $time) {
        try {
            $result = array(
                'comment_post_ID' => isset($psc_product_id[0]) ? $psc_product_id[0] : '',
                'comment_author' => isset($current_user->user_login) ? $current_user->user_login : '',
                'comment_author_email' => isset($current_user->user_email) ? $current_user->user_email : '',
                'comment_author_url' => '',
                'comment_author_IP' => '',
                'comment_date' => $time,
                'comment_date_gmt' => $time,
                'comment_content' => isset($psc_product_id[1]) ? $psc_product_id[1] : '',
                'comment_karma' => 0,
                'comment_approved' => 0,
                'comment_agent' => 'Pal-Shopping-Cart',
                'comment_type' => 'order_note',
                'comment_parent' => 0,
                'user_id' => 0
            );
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function psc_delete_order_note() {
        try {
            global $wpdb;
            check_ajax_referer('paypal_shopping_cart_url', 'security');
            $data_comment_id = !empty($_POST['value']) ? $_POST['value'] : false;
            $data_comment_id = isset($data_comment_id) ? $data_comment_id : '';
            $time_stemp_value = time();
            if (isset($data_comment_id) && !empty($data_comment_id)) {
                $commentarr = array();
                $commentarr['comment_ID'] = isset($data_comment_id) ? $data_comment_id : '';
                $commentarr['comment_approved'] = 'trash';
                wp_update_comment($commentarr);
                add_comment_meta($data_comment_id, '_' . $wpdb->prefix . 'trash_meta_time', $time_stemp_value);
                add_comment_meta($data_comment_id, '_' . $wpdb->prefix . 'trash_meta_status', '1');
            }
            echo json_encode('success');
            die();
        } catch (Exception $ex) {
            
        }
    }

}

PSC_AJAX_Handler::init();