<?php

class PSC_Common_Function {

    public $obj_session_heandler;
    public $obj_cart_heandler;

    public function __construct() {
        $this->obj_session_heandler = new PSC_Session_Handler();
        $this->obj_cart_heandler = new PSC_Cart_Handler();
    }

    public function psc_product_gallery_attachment_ids($post) {
        return apply_filters('psc_product_gallery_attachment_ids', array_filter((array) explode(',', $this->product_image_gallery)), $this);
    }

    public function psc_get_price_html($post) {
        $price = '';
        $get_array_result = $this->get_post_meta_all($post->ID);
        $price = $this->psc_del_and_ins_price($get_array_result);
        return $price;
    }

    public function psc_del_and_ins_price($get_array_result) {

        $price = "";
        $currency_code = get_option('psc_currency_general_settings');
        if (empty($currency_code)) {
            $currency_code = 'USD';
        }
        $currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
        if (isset($get_array_result['_psc_sale_price']) && !empty($get_array_result['_psc_sale_price'])) {
            if (isset($get_array_result['_psc_regular_price']) && !empty($get_array_result['_psc_regular_price'])) {
                $price = '<del><span class="amount">' . $currency_symbol . number_format($get_array_result['_psc_regular_price'], 2) . '</span></del>&nbsp;&nbsp;&nbsp;';
            }
            if (isset($get_array_result['_psc_sale_price']) && !empty($get_array_result['_psc_sale_price'])) {
                $price .='<ins><span class="amount">' . $currency_symbol . number_format($get_array_result['_psc_sale_price'], 2) . '</span></ins>';
            }
        } else {
            if (isset($get_array_result['_psc_regular_price']) && !empty($get_array_result['_psc_regular_price'])) {
                $price .='<ins><span class="amount">' . $currency_symbol . number_format($get_array_result['_psc_regular_price'], 2) . '</span></ins>';
            }
        }
        return $price;
    }

    public function psc_get_price_variable_and_simple($post) {

        $price = '';
        $price_del = '';
        $price_ins = '';
        $stock_data = "";
        $get_array_result = $this->get_post_meta_all($post->ID);
        $currency_code = get_option('psc_currency_general_settings');
        if (empty($currency_code)) {
            $currency_code = 'USD';
        }
        $currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);

        $display_outofstock_product = get_option('psc_shop_display_outofstock_product') ? get_option('psc_shop_display_outofstock_product') : 'yes';
        $is_display_product = true;
        $is_display_product_found = true;
        if (isset($get_array_result['psc-product-type-dropdown']) && 'variable' == $get_array_result['psc-product-type-dropdown']) {
//            $psc_variable_product_data = unserialize(trim($get_array_result['psc_variable_product_data']));
//            $psc_variable_product_data = unserialize($psc_variable_product_data);
            $psc_variable_product_data = $this->get_unserialize_data($get_array_result['psc_variable_product_data']);
            
            $options = '';
            foreach ($psc_variable_product_data as $key => $value) {
                if ($display_outofstock_product == 'no') {
                    $is_display_product = $this->display_outof_stock_product($key, $value);
                }
                if ($is_display_product) {
                    $options .='<option value="' . $value['psc_variable_product_name' . $key] . '">' . $value['psc_variable_product_name' . $key] . '</option>';
                    if ($is_display_product_found) {
                        $stock_data = $this->load_time_product_stock_check($key, $value, $currency_symbol);
                        $is_display_product_found = false;
                    }
                }
            }
            $price = $stock_data . '<div class="psc_select_variable_product_div"><select id="psc_select_variable_product" product-id="' . $post->ID . '" style="padding: 5px; margin-bottom: 10px">' . $options . '</select></div>';
        } else {
            $price = $this->psc_del_and_ins_price($get_array_result);
            $price = '<p class="price">' . $price . '</p>';
        }
        return $price;
    }

    public function load_time_product_stock_check($key, $value, $currency_symbol) {

        $result = "";
        $stock_data = "";
        if (isset($value['_psc_manage_stock_variable' . $key]) && '0' != $value['_psc_manage_stock_variable' . $key]) {
            $Product_stock = trim($value['psc_variable_product_stock' . $key]);
            if ($value['psc_variable_product_stock_status' . $key] == 1) {
                if (strlen($Product_stock) > 0) {
                    if ($Product_stock > 0) {
                        $stock_data = '<p class="stock in-stock">' . $Product_stock . ' In stock </p>';
                    } else {
                        $stock_data = '<p class="stock out-of-stock">Out of stock</p>';
                    }
                } else {
                    $stock_data = '<p class="stock in-stock"> In stock </p>';
                }
            } else if ($value['psc_variable_product_stock_status' . $key] == 0) {
                $stock_data = '<p class="stock out-of-stock">Out of stock</p>';
            }
        }
        $result = '<div class="psc-product-details-price"><p class="price">';
        $result .= $this->get_variable_product_del_and_ins_price($key, $value, $currency_symbol);
        $result .= '</p>' . $stock_data . '</div>';
        return $result;
    }

    public function get_variable_product_del_and_ins_price($key, $value, $currency_symbol) {

        $result = "";
        if (isset($value['psc_variable_product_sale_price' . $key]) && !empty($value['psc_variable_product_sale_price' . $key])) {
            $result .= '<del><span class="amount">' . $currency_symbol . number_format($value['psc_variable_product_regular_price' . $key], 2) . '</span></del>&nbsp;&nbsp;&nbsp;';
            $result .= '<ins><span class="amount">' . $currency_symbol . number_format($value['psc_variable_product_sale_price' . $key], 2) . '</span></ins>';
        } else {
            $result .= '<ins><span class="amount">' . $currency_symbol . number_format($value['psc_variable_product_regular_price' . $key], 2) . '</span></ins>';
        }
        return $result;
    }

    public function display_outof_stock_product($key, $value) {

        $result = false;
        if ($value['_psc_manage_stock_variable' . $key] == '1') {
            if ($value['psc_variable_product_stock_status' . $key] == '1') {
                if ($value['psc_variable_product_stock' . $key] != '0' || $value['psc_variable_product_stock' . $key] > '0') {
                $result = true;
            }
            }            
        } else {
            $result = true;
        }
        return $result;
    }

    public function psc_get_price($post) {

        $result = '';
        $result_data = $this->get_post_meta_data_postid($post);
        $is_stock_enable = false;
        $product_qty = 0;
        $display_outofstock_product = get_option('psc_shop_display_outofstock_product') ? get_option('psc_shop_display_outofstock_product') : 'yes';
        if (isset($result_data['psc-product-type-dropdown'][0]) && $result_data['psc-product-type-dropdown'][0] == 'simple') {
            if (isset($result_data['_psc_manage_stock_simple'][0]) && $result_data['_psc_manage_stock_simple'][0] == "yes") {
                $product_qty = $result_data['_psc_stock_qty_simple'][0];
                $is_stock_enable = true;
            } else if (isset($result_data['_psc_manage_stock_simple'][0]) && $result_data['_psc_manage_stock_simple'][0] == "no") {
                $is_stock_enable = true;
            }
        } else if (isset($result_data['psc-product-type-dropdown'][0]) && $result_data['psc-product-type-dropdown'][0] == 'variable') {
            $result_product = $this->get_post_meta_all($post->ID);
            $result_unsirealize = $this->get_unserialize_data($result_product['psc_variable_product_data']);
            foreach ($result_unsirealize as $key => $value) {
                if ($value['_psc_manage_stock_variable' . $key] == 1) {
                    if ($display_outofstock_product == 'no' && $value['psc_variable_product_stock' . $key] > 0) {
                        return ($value['psc_variable_product_sale_price' . $key]) ? $value['psc_variable_product_sale_price' . $key] : $value['psc_variable_product_regular_price' . $key];
                    } else if ($display_outofstock_product == 'yes') {
                        if ($value['psc_variable_product_stock' . $key] != 0) {
                            if ($value['psc_variable_product_stock_status' . $key] == 1) {
                                return ($value['psc_variable_product_sale_price' . $key]) ? $value['psc_variable_product_sale_price' . $key] : $value['psc_variable_product_regular_price' . $key];
                            } else if ($value['psc_variable_product_stock_status' . $key] == 0) {
                                return;
                            }
                        } else {
                            if ($value['psc_variable_product_stock_status' . $key] == 1) {
                                return ($value['psc_variable_product_sale_price' . $key]) ? $value['psc_variable_product_sale_price' . $key] : $value['psc_variable_product_regular_price' . $key];
                            } else if ($value['psc_variable_product_stock_status' . $key] == 0) {
                                return;
                            }
                            return;
                        }
                    }
                } else {
                    return ($value['psc_variable_product_sale_price' . $key]) ? $value['psc_variable_product_sale_price' . $key] : $value['psc_variable_product_regular_price' . $key];
                }
            }
        }
        if ($is_stock_enable == true) {
            if ((isset($result_data['_psc_sale_price'][0]) && !empty($result_data['_psc_sale_price'][0]))) {
                $result = isset($result_data['_psc_sale_price'][0]) ? $result_data['_psc_sale_price'][0] : $result_data['_psc_regular_price'][0];
            } else if ((isset($result_data['_psc_regular_price'][0]) && !empty($result_data['_psc_regular_price'][0]))) {
                $result = isset($result_data['_psc_regular_price'][0]) ? $result_data['_psc_regular_price'][0] : $result_data['_psc_sale_price'][0];
            } else {
                $result = "";
            }
        } else {
            $result = "";
        }
        return $result;
    }

    public function get_psc_currency() {
        $result = '';
        $result = get_option('psc_currency_general_settings');
        if (empty($result)) {
            $result = 'USD';
        }
        return $result;
    }

    public function get_psc_currency_symbol($currency_code) {
        $result = '';
        $result = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
        return $result;
    }

    public function get_psc_currency_symbol_only() {
        $result = '';
        $code = get_option('psc_currency_general_settings');
        if (empty($code)) {
            $code = 'USD';
        }
        $result = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($code);
        return $result;
    }

    public function get_post_meta_data_postid($post) {
        $result = '';
        $result = get_post_meta($post->ID);
        return $result;
    }

    public function psc_short_dec($post) {
        $result = '';
        $result_data = $this->get_post_meta_data_postid($post);
        if (isset($result_data['_wp_editor_test_1'][0]) && !empty($result_data['_wp_editor_test_1'][0])) {
            $result = $result_data['_wp_editor_test_1'][0];
        }
        return $result;
    }

    public function psc_get_sku($post) {
        $result = '';
        $result_data = $this->get_post_meta_data_postid($post);
        if (isset($result_data['_psc_sku'][0]) && !empty($result_data['_psc_sku'][0])) {
            $result = $result_data['_psc_sku'][0];
        }
        return $result;
    }

    public function psc_readmore_text($post) {
        return get_permalink();
    }

    public function psc_add_to_cart_url($post) {
        $url = remove_query_arg('added-to-cart', add_query_arg('add-to-cart', $post->ID));
        return apply_filters('psc_product_add_to_cart_url', $url, $post);
    }

    public function psc_get_product_type($post) {
        $result = '';
        $result_data = $this->get_post_meta_data_postid($post);
        if (isset($result_data['psc-product-type-dropdown'][0]) && !empty($result_data['psc-product-type-dropdown'][0])) {
            $result = $result_data['psc-product-type-dropdown'][0];
        }
        return $result;
    }

    public function psc_get_manage_stock($post) {

        $result = '';
        $product_stock_status = "";
        $result_data = $this->get_post_meta_all($post->ID);
        if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'simple') {
            if (isset($result_data['_psc_manage_stock_simple']) && $result_data['_psc_manage_stock_simple'] == 'yes') {
                $result = true;
            }
        } else if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'variable') {
            $result_unsirealize = $this->get_unserialize_data($result_data['psc_variable_product_data']);
            foreach ($result_unsirealize as $key => $value) {
                if ($value['_psc_manage_stock_variable0'] == 1) {
                    $product_stock_status = true;
                    break;
                } else {
                    $product_stock_status = false;
                    break;
                }
                break;
            }
            $result = $product_stock_status;
        }
        return $result;
    }

    public function psc_get_product_status($post) {
        $result = '';
        $result_data = $this->get_post_meta_all($post->ID);
        if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'simple') {
            if (isset($result_data['_psc_stock_status_simple']) && !empty($result_data['_psc_stock_status_simple'])) {
                $result = $result_data['_psc_stock_status_simple'];
            }
        } else if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'variable') {
            $result_unsirealize = $this->get_unserialize_data($result_data['psc_variable_product_data']);
            $product_stock_status = 'outofstock';
            foreach ($result_unsirealize as $key => $value) {
                if ($value['psc_variable_product_stock_status0'] == 1) {
                    $product_stock_status = 'instock';
                    break;
                }
                break;
            }
            $result = $product_stock_status;
        }
        return $result;
    }

    public function psc_add_to_cart_class($post) {
        $result = '';
        $result_data = $this->get_post_meta_data_postid($post);
        if (isset($result_data['_psc_regular_price'][0]) || isset($result_data['_psc_sale_price'][0])) {
            $result = "psc_add_to_cart_button";
        }
        return $result;
    }

    public function psc_add_to_cart_text($post) {

        $result = '';
        $result_data = $this->get_post_meta_all($post->ID);
        $product_qty = 0;
        if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'simple') {
            if ($result_data['_psc_manage_stock_simple'] == "yes") {
                $product_qty = trim($result_data['_psc_stock_qty_simple']);
                $product_status = $result_data['_psc_stock_status_simple'];
                if (isset($product_status) && $product_status == 'instock') {
                    if (isset($product_qty) && strlen($product_qty) > 0) {
                        if (isset($product_qty) && $product_qty > 0) {
                            return apply_filters('psc_button_text', __("Add to cart", "pal-shopping-cart"));
                        } else {
                            return apply_filters('psc_button_text', __("Read More", "pal-shopping-cart"));
                        }
                    } else {
                        return apply_filters('psc_button_text', __("Add to cart", "pal-shopping-cart"));
                    }
                } else if (isset($product_status) && $product_status == 'outofstock') {

                    return apply_filters('psc_button_text', __("Read More", "pal-shopping-cart"));
                }
            } else if (isset($result_data['_psc_regular_price']) && !empty($result_data['_psc_regular_price'])) {
                return apply_filters('psc_button_text', __("Add to cart", "pal-shopping-cart"));
            }
        } else if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'variable') {
            return apply_filters('psc_select_option_text', __("Select Option", "pal-shopping-cart"));
        }
    }

    public function get_post_meta_data_product_id($post_id) {
        $result = array();
        $result = get_post_meta($post_id);
        if (strlen($result['post_name'][0]) == 0) {
            $name = get_the_title($post_id);
            $result['post_name'][0] = $name;
        }
        return $result;
    }

    public function cart_get_image($id) {
        $size = 'psc_shop_thumbnail';
        $placeholder = true;
        $attr = array();
        if (has_post_thumbnail($id)) {
            $image = get_the_post_thumbnail($id, $size, $attr);
        } elseif (( $parent_id = wp_get_post_parent_id($id) ) && has_post_thumbnail($parent_id)) {
            $image = get_the_post_thumbnail($parent_id, $size, $attr);
        } elseif ($placeholder) {
            //$image = placeholder_img_src($size);
			 $image = "<img src='" . apply_filters('psc_placeholder_img_src', placeholder_img_src($size)) . "'>";
        } else {
            $image = "<img src='" . apply_filters('psc_placeholder_img_src', plugins_url('/admin/images/placeholder.png', dirname(__FILE__))) . "'>";
        }
        return $image;
    }

    public function get_permalink($id) {
        return get_permalink($id);
    }

    public function is_enable_payment_methods() {


        $paypal_gateways = array();
        $all_methods_array = array();
        $paypal_express_checkout = (get_option('psc_pec_enabled') == 'yes') ? true : false;
        $paypal_pro = (get_option('psc_pec_paypal_pro_enabled') == 'yes') ? true : false;
        $paypal_pro_payflow = (get_option('psc_pec_paypal_pro_payflow_enabled') == 'yes') ? true : false;
        $paypal_advanced = (get_option('psc_pec_paypal_advanced_enabled') == 'yes') ? true : false;

        $all_methods_array = array(
            'psc_pec_enabled' => $paypal_express_checkout,
            'psc_pec_paypal_pro_enabled' => $paypal_pro,
            'psc_pec_paypal_pro_payflow_enabled' => $paypal_pro_payflow,
            'psc_pec_paypal_advanced_enabled' => $paypal_advanced
        );

        foreach ($all_methods_array as $key => $value) {
            $paypal_gateway = array();
            if ($value) {
                if ($this->get_required_field_paypal_gateways($key)) {
                    $key_option = str_replace('_enabled', '', $key);
                    $paypal_gateway['enable'] = $value;
                    $paypal_gateway['method'] = $this->set_paypal_methods_name($key);
                    $paypal_gateway['icon'] = $this->set_paypal_methods_icon($key);
                    $paypal_gateway['name'] = get_option($key_option . '_title') ? get_option($key_option . '_title') : 'Title';
                    $paypal_gateway['discription'] = $this->set_paypal_methods_discription($key_option, $key);
                    array_push($paypal_gateways, $paypal_gateway);
                }
            }
        }
        return $paypal_gateways;
    }

    public function get_required_field_paypal_gateways($gateway_name) {

        $gateway_name_option = str_replace('_enabled', '', $gateway_name);
        $is_test_mode = (get_option($gateway_name_option . '_testmode') == 'yes') ? true : false;
        $result = false;
        if ($is_test_mode) {
            if ($gateway_name == 'psc_pec_enabled') {

                $api_username = (get_option('psc_pec_sandbox_api_username')) ? true : false;
                $api_password = (get_option('psc_pec_sandbox_api_password')) ? true : false;
                $api_signature = (get_option('psc_pec_sandbox_api_signature')) ? true : false;
                if ($api_username && $api_password && $api_signature) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_pro_enabled') {
                $api_username = (get_option('psc_pec_paypal_pro_sandbox_api_username')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_pro_sandbox_api_password')) ? true : false;
                $api_signature = (get_option('psc_pec_paypal_pro_sandbox_api_signature')) ? true : false;
                if ($api_username && $api_password && $api_signature) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_pro_payflow_enabled') {
                $api_username = (get_option('psc_pec_paypal_pro_payflow_sandbox_vendor')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_pro_payflow_sandbox_password')) ? true : false;
                if ($api_username && $api_password) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_advanced_enabled') {
                $api_username = (get_option('psc_pec_paypal_advanced_sandbox_merchant')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_advanced_sandbox_password')) ? true : false;
                if ($api_username && $api_password) {
                    $result = true;
                }
            }
        } else {

            if ($gateway_name == 'psc_pec_enabled') {
                $api_username = (get_option('psc_pec_api_username')) ? true : false;
                $api_password = (get_option('psc_pec_api_password')) ? true : false;
                $api_signature = (get_option('psc_pec_api_signature')) ? true : false;
                if ($api_username && $api_password && $api_signature) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_pro_enabled') {
                $api_username = (get_option('psc_pec_paypal_pro_live_api_username')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_pro_live_api_password')) ? true : false;
                $api_signature = (get_option('psc_pec_paypal_pro_live_api_signature')) ? true : false;
                if ($api_username && $api_password && $api_signature) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_pro_payflow_enabled') {
                $api_username = (get_option('psc_pec_paypal_pro_payflow_live_vendor')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_pro_payflow_live_password')) ? true : false;
                if ($api_username && $api_password && $api_signature) {
                    $result = true;
                }
            } else if ($gateway_name == 'psc_pec_paypal_advanced_enabled') {
                $api_username = (get_option('psc_pec_paypal_advanced_live_merchant')) ? true : false;
                $api_password = (get_option('psc_pec_paypal_advanced_live_password')) ? true : false;
                if ($api_username && $api_password) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    public function set_paypal_methods_name($key) {
        $result = '';
        if ('psc_pec_enabled' == $key) {
            $result = 'PayPal_Express_Checkout_Method';
        } else if ('psc_pec_paypal_pro_enabled' == $key) {
            $result = 'PayPal_Pro_Method';
        } else if ('psc_pec_paypal_pro_payflow_enabled' == $key) {
            $result = 'PayPal_Pro_PayFlow_Method';
        } else if ('psc_pec_paypal_advanced_enabled' == $key) {
            $result = 'PayPal_Advanced';
        }
        return $result;
    }

    public function set_paypal_methods_icon($key) {
        $result = '';
        if ('psc_pec_enabled' == $key) {
            $result = plugins_url() . '/pal-shopping-cart/public/images/paypal-credit.png';
        } else if ('psc_pec_paypal_pro_enabled' == $key) {
            $result = plugins_url() . '/pal-shopping-cart/public/images/credit.png';
        } else if ('psc_pec_paypal_pro_payflow_enabled' == $key) {
            $result = plugins_url() . '/pal-shopping-cart/public/images/credit.png';
        } else if ('psc_pec_paypal_advanced_enabled' == $key) {
            $result = plugins_url() . '/pal-shopping-cart/public/images/credit.png';
        }
        return $result;
    }

    public function set_paypal_methods_discription($key_option, $key) {
        $result = '';
        if ('psc_pec_enabled' == $key) {
            $result = get_option($key_option . '_description') ? get_option($key_option . '_description') : '';
        } else if ('psc_pec_paypal_pro_enabled' == $key) {
            $discription = get_option($key_option . '_description') ? '<p>' . get_option($key_option . '_description') . '</p>' : '';
            $result = $discription . '<div class="payment_gateways_credit_card_form" ><p><input type="text" class="checkout-input checkout-name" placeholder="Your name" id="pro_card_holder_name" name="pro_card_holder_name" autofocus></p><p><input type="text" class="checkout-input checkout-card" id="pro_card_number" name="pro_card_number" data-name="number" placeholder="4111 1111 1111 1111"></p><p><input type="text" class="checkout-input checkout-exp" data-name="ex_mm" id="pro_card_ex_mm" name="pro_card_ex_mm" placeholder="MM"><input type="text" class="checkout-input checkout-exp" id="pro_card_ex_yy" data-name="ex_yy" name="pro_card_ex_yy" placeholder="YYYY"><input type="text" class="checkout-input checkout-cvc" id="pro_card_cvc" data-name="cvc" name="pro_card_cvc" placeholder="CVC"></p></div>';
        } else if ('psc_pec_paypal_pro_payflow_enabled' == $key) {
            $discription = get_option($key_option . '_description') ? '<p>' . get_option($key_option . '_description') . '</p>' : '';
            $result = $discription . '<div class="payment_gateways_credit_card_form" ><p><input type="text" class="checkout-input checkout-name" placeholder="Your name" id="card_holder_name" name="card_holder_name" autofocus></p><p><input type="text" class="checkout-input checkout-card" id="card_number" name="card_number" data-name="number" placeholder="4111 1111 1111 1111"></p><p><input type="text" class="checkout-input checkout-exp" id="card_ex_mm" name="card_ex_mm" data-name="ex_mm" placeholder="MM"><input type="text" class="checkout-input checkout-exp" id="card_ex_yy" data-name="ex_yy" name="card_ex_yy" placeholder="YYYY"><input type="text" class="checkout-input checkout-cvc" id="card_cvc" name="card_cvc" data-name="cvc" placeholder="CVC"></p></div>';
        } else if ('psc_pec_paypal_advanced_enabled' == $key) {
            $result = get_option($key_option . '_description') ? '<p>' . get_option($key_option . '_description') . '</p>' : '';            
        }
        return $result;
    }

    public function is_empty_filed_found() {
        global $empty_filed;
        $empty_filed = '';
        if (isset($_POST['submit'])) {
            if (!empty($_POST['billing_first_name'])) {
                $empty_filed['first_name'] = sanitize_text_field($_POST['billing_first_name']);
            } else {
                $empty_filed['first_name'] = 'empty';
            }
            if (!empty($_POST['billing_last_name'])) {
                $empty_filed['last_name'] = sanitize_text_field($_POST['billing_last_name']);
            } else {
                $empty_filed['last_name'] = 'empty';
            }
            if (!empty($_POST['billing_email'])) {
                $empty_filed['email'] = sanitize_email($_POST['billing_email']);
            } else {
                $empty_filed['email'] = 'empty';
            }
            if (!empty($_POST['billing_phone'])) {
                $empty_filed['phone'] = intval($_POST['billing_phone']);
            } else {
                $empty_filed['phone'] = 'empty';
            }
            if (!empty($_POST['billing_country']) && $_POST['billing_country'] != 'select') {
                $empty_filed['country'] = sanitize_text_field($_POST['billing_country']);
            } else {
                $empty_filed['country'] = 'empty';
            }
            if (!empty($_POST['billing_address_1'])) {
                $empty_filed['address1'] = sanitize_text_field($_POST['billing_address_1']);
            } else {
                $empty_filed['address1'] = 'empty';
            }
            if (!empty($_POST['billing_city'])) {
                $empty_filed['city'] = sanitize_text_field($_POST['billing_city']);
            } else {
                $empty_filed['city'] = 'empty';
            }
            if (!empty($_POST['billing_state'])) {
                $empty_filed['state'] = sanitize_text_field($_POST['billing_state']);
            } else {
                $empty_filed['state'] = 'empty';
            }
            if (!empty($_POST['billing_postcode'])) {
                $empty_filed['postcode'] = intval($_POST['billing_postcode']);
            } else {
                $empty_filed['postcode'] = 'empty';
            }
            $this->redirect_payment_getway($empty_filed);
        }
        return;
    }

    public function redirect_payment_getway($empty_filed) {
        try {
            $result = true;
            foreach ($empty_filed as $key => $value) {
                if ($value == 'empty') {
                    $result = false;
                    break;
                }
            }
            if ($result == true) {
                if (isset($_POST['psc_payment_method']) && $_POST['psc_payment_method'] == 'PayPal_Express_Checkout_Method') {
                    $methods_obj = new Paypal_Shopping_Cart_Express_Checkout();
                    $methods_obj->paypal_express_checkout($_POST);
                } else if (isset($_POST['psc_payment_method']) && $_POST['psc_payment_method'] == 'PayPal_Pro_Method') {
                     $methods_obj = new Paypal_Shopping_Cart_PayPal_Pro();
                     $methods_obj->pay_shopping_cart_pro_process_payment($_POST);
                } else if (isset($_POST['psc_payment_method']) && $_POST['psc_payment_method'] == 'PayPal_Pro_PayFlow_Method') {
                    $methods_obj = new Paypal_Shopping_Cart_PayPal_Pro_Payflow();
                    $methods_obj->pay_shopping_cart_pro_payflow_process_payment($_POST);
                } else if (isset($_POST['psc_payment_method']) && $_POST['psc_payment_method'] == 'PayPal_Advanced') {                    
                    if (!class_exists('Paypal_Shopping_Cart_PayPal_Advanced')) {
                        require_once( PAYPAL_FOR_PAYPAL_SHOPPING_CART_PLUGIN_DIR . '/public/partials/paypal-shopping-cart-paypal-advanced.php' );
                    }
                    $methods_obj = new Paypal_Shopping_Cart_PayPal_Advanced();
                    $methods_obj->pal_shopping_cart_paypal_advanced_process_payment($_POST);
                }
            }
        } catch (Exception $ex) {
            
        }
    }

    public function get_result_of_order_received_data($orderid) {
        global $wpdb;
        $result_meta = "";
        $array_of_order_detail = array();
        $post_id = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s", '_uniq_id_generatore', $orderid));
        if (isset($post_id[0]->post_id) && !empty($post_id[0]->post_id)) {
            $result_meta = $this->get_post_meta_all($post_id[0]->post_id);
            $array_of_order_detail['_orderid'] = $post_id[0]->post_id;
            $array_of_order_detail['_orderdate'] = get_the_date('d-F-Y', $post_id[0]->post_id);
            array_push($result_meta, $array_of_order_detail);
        }
        return $result_meta;
    }

    public function get_post_meta_all($post_id) {
        global $wpdb;
        $data = array();
        $wpdb->query(" SELECT `meta_key`, `meta_value` FROM $wpdb->postmeta WHERE `post_id` = $post_id");
        foreach ($wpdb->last_result as $k => $v) {
            $data[$v->meta_key] = $v->meta_value;
        }
        return $data;
    }

    public function two_digit_get_statecode_to_state($country_code, $statecode) {

        global $states;
        $state_name = '';
        if ((isset($country_code) && !empty($country_code)) && (isset($statecode) && !empty($statecode))) {
            if (file_exists(PSC_PLUGIN_DIR_PATH . '/templates/countries/states/' . $country_code . '.php')) {
                require PSC_PLUGIN_DIR_PATH . '/templates/countries/states/' . $country_code . '.php';
            }
            foreach ($states as $key => $value) {
                foreach ($value as $state_code => $state_name) {
                    if ($state_code == $statecode) {
                        return $state_name;
                    }
                }
            }
        }
        return $state_name;
    }

    public function two_digit_get_countrycode_to_country($country_code) {
        $country_obj = new PSC_Countries();
        $country_array = $country_obj->Countries();
        foreach ($country_array as $key => $value) {
            if ($key == $country_code) {
                return $value;
            }
        }
        return;
    }

    public function get_customer_address() {
        $result = '';
        $shiptoname = explode(' ', $this->session_get('shiptoname'));
        $firstname = esc_html($shiptoname[0]);
        $lastname = esc_html($shiptoname[1]);
        $result .= esc_html($firstname) . ' ' . esc_html($lastname) . '<br>';
        $result .= esc_html($this->session_get('shiptostreet')) . '<br>';
        $result .= ($this->session_get('shiptostreet2')) ? esc_html($this->session_get('shiptostreet2')) . '<br> ' : '';
        $result .= esc_html($this->session_get('shiptocity') . ', ' . $this->session_get('shiptostate') . '(' . $this->two_digit_get_statecode_to_state($this->session_get('shiptocountrycode'), $this->session_get('shiptostate')) . ') ' . $this->session_get('shiptozip')) . '<br>';
        $result .= esc_html($this->two_digit_get_countrycode_to_country($this->session_get('shiptocountrycode')));
        return $result;
    }

    public function get_unserialize_data($data) {
        $result = array();
        $data = unserialize(trim($data));                
        $fixed_serialized_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $data );
        $result = unserialize( $fixed_serialized_data );
        return $result;
    }

    public function is_enable_stock_management($post) {

        $result_data = $this->get_post_meta_all($post->ID);
        $is_stock_enable = false;
        if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'simple') {
            if (isset($result_data['_psc_manage_stock_simple']) && $result_data['_psc_manage_stock_simple'] == "yes") {
                $is_stock_enable = true;
            }
        } else if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'variable') {
            $result_unsirealize = $this->get_unserialize_data($result_data['psc_variable_product_data']);
            foreach ($result_unsirealize as $key => $value) {
                if ($value['_psc_manage_stock_variable' . $key] == 1) {
                    $is_stock_enable = true;
                    break;
                }
                break;
            }
        }
        return $is_stock_enable;
    }

    public function is_enable_stock_management_old($post) {

        $result_data = $this->get_post_meta_data_postid($post);
        $is_stock_enable = false;
        if (isset($result_data['psc-product-type-dropdown'][0]) && $result_data['psc-product-type-dropdown'][0] == 'simple') {
            if ($result_data['_psc_manage_stock_simple'][0] == "yes") {
                $is_stock_enable = true;
            }
        } else if (isset($result_data['psc-product-type-dropdown'][0]) && $result_data['psc-product-type-dropdown'][0] == 'variable') {
            if ($result_data['_psc_manage_stock_variable'][0] == "yes") {
                $is_stock_enable = true;
            }
        }
        return $is_stock_enable;
    }

    public function psc_get_product_stock($post) {

        $result = '';
        $result_data = $this->get_post_meta_all($post->ID);
        $is_stock_enable = false;
        $product_qty = 0;
        if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'simple') {
            if ($result_data['_psc_manage_stock_simple'] == "yes") {
                $product_qty = $result_data['_psc_stock_qty_simple'];
                $is_stock_enable = true;
            }
        } else if (isset($result_data['psc-product-type-dropdown']) && $result_data['psc-product-type-dropdown'] == 'variable') {
            $result_unsirealize = $this->get_unserialize_data($result_data['psc_variable_product_data']);
            foreach ($result_unsirealize as $key => $value) {
                if ($value['_psc_manage_stock_variable' . $key] == 1) {
                    $product_qty = ($value['psc_variable_product_stock' . $key]) ? $value['psc_variable_product_stock' . $key] : 0;
                    $is_stock_enable = true;
                    break;
                }
                break;
            }
        }
        if ($is_stock_enable == true) {
            if ((isset($product_qty) && '0' != $product_qty)) {
                $result = $product_qty;
            } else {
                $result = $product_qty;
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public function get_order_all_details($post_id) {
        $result_array = $this->get_post_meta_all($post_id);
        $order_responce = '';
        $psc_cart_serialize = '';
        $final_merge_array = array();
        if (is_array($result_array) && count($result_array) > 0) {
            if (isset($result_array['_shipping_state']) && !empty($result_array['_shipping_state'])) {
                $state_name = $this->two_digit_get_statecode_to_state($result_array['_shipping_country'], $result_array['_shipping_state']);
                unset($result_array['_shipping_state']);
                $result_array['_shipping_state'] = $state_name;
            }
            if (isset($result_array['_shipping_country']) && !empty($result_array['_shipping_country'])) {
                $country_name = $this->two_digit_get_countrycode_to_country($result_array['_shipping_country']);
                unset($result_array['_shipping_country']);
                $result_array['_shipping_country'] = $country_name;
            }
            if (isset($result_array['_order_responce'])) {
                $order_responce = $this->get_unserialize_data($result_array['_order_responce']);
                unset($result_array['_order_responce']);
            }
            if (isset($result_array['_psc_cart_serialize'])) {
                $psc_cart_serialize = $this->get_unserialize_data($result_array['_psc_cart_serialize']);
                unset($result_array['_psc_cart_serialize']);
            }
            array_push($final_merge_array, $result_array);
            array_push($final_merge_array, $order_responce);
            array_push($final_merge_array, $psc_cart_serialize);
        }
        return $final_merge_array;
    }

    public function get_order_item_details($post_id) {
        $result_array = $this->get_post_meta_all($post_id);
        $order_item_array = array();
        $currency_code = $this->get_psc_currency();
        $currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
        $total = 0;
        if (isset($result_array['_psc_cart_serialize'])) {
            $psc_cart_serialize = $this->get_unserialize_data($result_array['_psc_cart_serialize']);
            foreach ($psc_cart_serialize as $key => $value) {
                $array_Item = array();
                $array_Item['id'] = $value['id'];
                $array_Item['name'] = $value['name'];
                $array_Item['price'] = $value['price'];
                $array_Item['qty'] = $value['qty'];
                $array_Item['subtotal'] = $value['subtotal'];
                $array_Item['url'] = $this->make_product_image_by_id($value['id']);
                $total = $total + $value['subtotal'];
                array_push($order_item_array, $array_Item);
            }
        }
        
//        $coupon_cart_discount_array = unserialize(trim($result_array['_order_cart_discount']));        
//        $order_item_array['order_cart_discount'] = unserialize($coupon_cart_discount_array);        
        $order_item_array['order_cart_discount'] = $this->get_unserialize_data($result_array['_order_cart_discount']);
        
        //$order_item_array['order_cart_discount_coupon_code'] = $result_array['_order_cart_discount_coupon_code'];
        $order_item_array['final_total'] = $total;
        $order_item_array['currency_symbol'] = $currency_symbol;
        return $order_item_array;
    }

    public function make_product_image_by_id($id) {
        $url = "";
        $url_data = $this->cart_get_image($id);
        preg_match('/src=(\'|")+[^(\'|")]+/', $url_data, $image_url);
        if (isset($image_url[0])) {
            $url = preg_replace('/src=(\'|")/', "", $image_url[0]);
        }
        if (isset($url) && empty($url)) {
            $url = PSC_FOR_WORDPRESS_LOG_DIR . 'admin/images/placeholder.png';
        }
        return $url;
    }

    public function session_set($key, $value) {
        $this->obj_session_heandler->set_userdata($key, $value);
    }

    public function session_get($key) {
        return $this->obj_session_heandler->userdata($key);
    }

    public function session_remove($key) {
        $this->obj_session_heandler->unset_userdata($key);
    }

    public function session_cart_contents() {
        return $this->obj_cart_heandler->contents();
    }

    public function session_cart_contents_total($key) {
        return $this->obj_cart_heandler->_cart_contents[$key];
    }

    public function session_cart_destroy() {
        $this->obj_cart_heandler->destroy();
    }

    public function customer_session_empty() {
        $this->session_remove('TOKEN');
        $this->session_remove('PayerID');
        $this->session_remove('chosen_shipping_methods');
        $this->session_remove('company');
        $this->session_remove('firstname');
        $this->session_remove('lastname');
        $this->session_remove('shiptoname');
        $this->session_remove('shiptostreet');
        $this->session_remove('shiptostreet2');
        $this->session_remove('shiptocity');
        $this->session_remove('shiptocountrycode');
        $this->session_remove('shiptostate');
        $this->session_remove('shiptozip');
        $this->session_remove('payeremail');
        $this->session_remove('customer_notes');
        $this->session_remove('phonenum');
        $this->session_remove('order_id');
       // $this->session_remove('coupon_cart_discount');
       // $this->session_remove('coupon_code');
        $this->session_remove('coupon_cart_discount_msg');        
    }

    public function psc_write_log_activity($handle, $message) {
        $psclog = new Paypal_Shopping_Cart_Logger();
        $psclog->add($handle, $message);
    }

    public function psc_write_log_activity_array($handle, $title, $message) {
        $psclog = new Paypal_Shopping_Cart_Logger();
        $error_key = $message['ERRORS'];
        unset($message['RAWREQUEST']);
        $message['REQUESTDATA'] = $this->pattern_to_star($message['REQUESTDATA']);
        $psclog->add($handle, $title . print_r($message, true));
        return;
    }

    public function pattern_to_star($result) {
        foreach ($result as $key => $value) {
            if ("USER" == $key || "PWD" == $key || "BUTTONSOURCE" == $key || "SIGNATURE" == $key || "EMAIL" == $key) {
                $str_length = strlen($value);
                $ponter_data = "";
                for ($i = 0; $i <= $str_length; $i++) {
                    $ponter_data .= '*';
                }
                $result[$key] = $ponter_data;
            }
        }
        return $result;
    }

    public function psc_get_page_id_by_title($title) {
        $result = '';
        $result = get_page_by_title($title);
        return $result;
    }

    public function psc_pscrevieworder_page($title) {
        $result = '';
        $result = get_page_by_title($title);
        $result = isset($result->ID) ? $result->ID : '';
        return $result;
    }

    public function psc_checkout_page() {
        $result = '';
        $result = get_option('psc_checkoutpage_product_settings');
        if (empty($result)) {
            $checkoutpage = get_page_by_title('Checkout');
            $result = $checkoutpage->ID;
        }
        return $result;
    }

    public function psc_cart_page() {
        $result = '';
        $result = get_option('psc_cartpage_product_settings');
        if (empty($result)) {
            $cartpage = get_page_by_title('Cart');
            $result = $cartpage->ID;
        }
        return $result;
    }

    public function psc_shop_page() {
        $result = '';
        $result = get_option('psc_shoppage_product_settings');
        if (empty($result)) {
            $shoppage = get_page_by_title('Shop');
            $result = $shoppage->ID;
        }
        return $result;
    }

    public function psc_cancel_page_url(){
        $result = '';
        $result = (get_option('psc_pec_cancel_page'))?get_option('psc_pec_cancel_page'):'';

        if ("page" == $result || empty($result)) {
            $cancelpage = get_page_by_title('Cart');
            $result = $cancelpage->ID;
        }
        return $result;
    }

    public function psc_addtocart_after_redirect_page() {
        $result = '';
        $result = (get_option('psc_addtocart_after_general_settings')) ? get_option('psc_addtocart_after_general_settings') : '';
        if ("page" == $result || empty($result)) {
            $cartpage = get_page_by_title('Cart');
            $result = $cartpage->ID;
        }
        return $result;
    }

    public function add_cart_after_redirect_behaviour() {
        $result = '';
        $result = (get_option('psc_addtocart_after_general_settings')) ? get_option('psc_addtocart_after_general_settings') : '';
        if ((isset($result) && empty($result)) || $result == 'page') {
            $result = "";
        } 
        return $result;
    }

    public function get_all_coupons_with_match($coupon_title) {
        global $wpdb;
        $result = array();  
        $result['code_match'] = false;
        
        $result_array = get_page_by_title( $coupon_title,'','psc_coupon' );
        
        if(isset($result_array) && !empty($result_array)){
            $postmeta_data = $this->get_post_meta_all($result_array->ID);
           
             if (isset($postmeta_data['psc_coupon_expiry_date']) && date("Y-m-d") <= $postmeta_data['psc_coupon_expiry_date']) {
                 
                $result['psc_coupon_amount'] = ($postmeta_data['psc_coupon_amount'])?$postmeta_data['psc_coupon_amount']:'';
                $result['psc_coupon_expiry_date'] = ($postmeta_data['psc_coupon_expiry_date'])?$postmeta_data['psc_coupon_expiry_date']:'';
                $result['psc_coupon_discount_type'] = ($postmeta_data['psc_coupon_discount_type'])?$postmeta_data['psc_coupon_discount_type']:'';
                $result['coupon_code'] = strtoupper(($postmeta_data['post_title'])?$postmeta_data['post_title']:'');
                $result['post_id'] = $result_array->ID;
                $result['code_match'] = true;
                $result['psc_coupon_status'] = 'success';                
            } else {
                $result['psc_coupon_status'] = 'This coupon has expired.';
            }            
        } else {
            $result['psc_coupon_status'] = 'Coupon "' . $coupon_title . '" does not exist!';
        }       
        return $result;
    }

    public function get_all_coupons_with_match_old($coupon_code) {
        global $wpdb;
        $result = array();
        $result['code_match'] = false;
        $result['coupon_code'] = $coupon_code;
        $psc_coupons = $wpdb->get_results("SELECT * FROM `" . $wpdb->posts . "` WHERE `post_type` = 'psc_coupon' and `post_status` = 'publish'");
        if (is_array($psc_coupons) && count($psc_coupons) > 0) {
            foreach ($psc_coupons as $key => $value) {
                if (trim($value->post_title) == trim($coupon_code)) {
                    $postmeta_data = $this->get_post_meta_all($value->ID);
                    if ( isset($postmeta_data['psc_coupon_expiry_date']) && date("Y-m-d") <= $postmeta_data['psc_coupon_expiry_date']) {
                        $result['code_match'] = true;
                        $result['psc_coupon_amount'] = ($postmeta_data['psc_coupon_amount'])?$postmeta_data['psc_coupon_amount']:'';
                        $result['psc_coupon_expiry_date'] = ($postmeta_data['psc_coupon_expiry_date'])?$postmeta_data['psc_coupon_expiry_date']:'';
                        $result['psc_coupon_status'] = 'success';
                    } else {
                        $result['psc_coupon_status'] = 'This coupon has expired.';
                    }
                } else {
                    $result['psc_coupon_status'] = 'Coupon "' . $coupon_code . '" does not exist!';
                }
            }
        } else {
            $result['psc_coupon_status'] = 'Coupon "' . $coupon_code . '" does not exist!';
        }
        return $result;
    }

    public function psc_is_enable_coupons() {
        $result = '';
        $result = (get_option('psc_coupons_general_settings')) == 'yes' ? true : false;
        return $result;
    }

    public function get_cart_total_discount() {
        $result = 0;
        $discount_amount = $this->session_get('coupon_cart_discount');
        if (isset($discount_amount) && '0' != $discount_amount) {
            $result = $discount_amount;
        }
        return $result;
    }

    public function get_cart_total_coupon_code() {
        $result = '';
        $coupon_code = $this->session_get('coupon_code');
        if (isset($coupon_code) && !empty($coupon_code)) {
            $result = $coupon_code;
        }
        return $result;
    }

    public function get_cart_array() {
        $result = array();
        $discount_arrar = array();
        $cart_total = $this->session_cart_contents_total('cart_total');
        $cart = $this->session_cart_contents();
        $discount_arrar = $this->remove_cart_coupon_in_array('');//$this->get_cart_total_discount();
        $result['itemamt'] = $this->number_format($cart_total, $this->get_psc_currency());
        $cart_array = array();
        $is_shipping_amount = 0;
        if ( is_array($cart) && count($cart) > 0 ) {
            foreach ($cart as $key => $value) {
                $result_data = '';
                $result_data['id'] = $value['id'];
                $result_data['name'] = $value['name'];
                $result_data['amt'] = $value['price'];
                $result_data['number'] = $value['rowid'];
                $result_data['qty'] = $value['qty'];
                $qty_ship = $value['qty'] * $value['shipping'];
                $is_shipping_amount = $is_shipping_amount + $qty_ship;
                array_push($cart_array, $result_data);
            }
        }

        if( isset($is_shipping_amount) && !empty($is_shipping_amount) ){            
            $result_data = '';
            $result_data['name'] = 'Shipping: ';
            $result_data['amt'] = $this->number_format($is_shipping_amount, $this->get_psc_currency());
            $result_data['qty'] = '1';
                array_push($cart_array, $result_data);
            
            }      
        $result['shippingamt'] = $this->number_format($is_shipping_amount, $this->get_psc_currency());
        if(is_array($discount_arrar) && count($discount_arrar) > 0){
            $discount_temp = 0;
            foreach ($discount_arrar as $key_d => $value_d) {
            $result_data = '';
                $result_data['name'] = 'Discount Code: ' . $value_d['coupon_code'];
                $result_data['amt'] = '-' . $this->number_format($value_d['psc_coupon_amount'], $this->get_psc_currency());
                $result_data['number'] = $value_d['coupon_code'];
            $result_data['qty'] = '1';
                $discount_temp = $discount_temp + $value_d['psc_coupon_amount'];
            array_push($cart_array, $result_data);
            }
            
            $result['itemamt'] = $this->number_format($cart_total - $discount_temp, $this->get_psc_currency());
            //number_format($cart_total - $discount_temp, 2);
        }
 
        $result['itemamt'] = $this->number_format($result['itemamt'] + $is_shipping_amount, $this->get_psc_currency());
        //number_format($result['itemamt'] + $is_shipping_amount, 2);
        
        $result['cart_item'] = $cart_array;
        
        return $result;
    }

    public function get_stock_by_post_id($post_id) {
        $result = 0;
        $meta_array = $this->get_post_meta_all($post_id);

        if (isset($meta_array['psc-product-type-dropdown']) && $meta_array['psc-product-type-dropdown'] == 'simple') {
            $result = isset($meta_array['_psc_stock_qty_simple']) ? $meta_array['_psc_stock_qty_simple'] : '';
        } else if (isset($meta_array['psc-product-type-dropdown']) && $meta_array['psc-product-type-dropdown'] == 'variable') {
//	    $psc_variable_product_data = ($meta_array['psc_variable_product_data'])?$meta_array['psc_variable_product_data']:'';
//            $psc_variable_product_data = unserialize(trim($psc_variable_product_data));
//            $psc_variable_product_data = unserialize($psc_variable_product_data);
            
            $psc_variable_product_data = $this->get_unserialize_data($meta_array['psc_variable_product_data']);
            $result = $psc_variable_product_data[0]['psc_variable_product_stock0'];
        }
        return $result;
    }

    public function get_stock_status_by_post_id($post_id) {

        $result = false;
        $meta_array = $this->get_post_meta_all($post_id);
        if ($meta_array['psc-product-type-dropdown'] == 'simple') {
            if ($meta_array['_psc_manage_stock_simple'] == 'yes') {
                if ($meta_array['_psc_stock_status_simple'] != 'outofstock' ) {
                if ($meta_array['_psc_stock_qty_simple'] > 0) {
                    $result = true;
                }
                }                
            } else {
                if ($meta_array['_psc_stock_status_simple'] == 'instock') {
                    $result = true;
                }
            }
        } else if ($meta_array['psc-product-type-dropdown'] == 'variable') {
            
//            $psc_variable_product_data = unserialize(trim($meta_array['psc_variable_product_data']));
//            $psc_variable_product_data = unserialize($psc_variable_product_data);
            $psc_variable_product_data = $this->get_unserialize_data($meta_array['psc_variable_product_data']);
            
            foreach ($psc_variable_product_data as $key => $value) {
                if ($value['_psc_manage_stock_variable' . $key] == '1') {
                    if ( $value['psc_variable_product_stock_status' . $key] == 1 ) {
                        if ($value['psc_variable_product_stock' . $key] != 0 ||$value['psc_variable_product_stock' . $key] > 0) {
                        $result = true;
                        break;
                    }
                } else {
                        break;
                    }                   
                } else {
                    if ($value['psc_variable_product_stock_status'. $key] == '1') {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function get_update_stock_by_post_id($post_id, $pname = "") {

        $result = '';
        $meta_array = $this->get_post_meta_all($post_id);
        if (is_array($meta_array) && count($meta_array) == 0) {
            return;
        }
        if ($meta_array['psc-product-type-dropdown'] == 'simple') {
            if ($meta_array['_psc_manage_stock_simple'] == 'yes') {
                $product_stock = trim($meta_array['_psc_stock_qty_simple']);
                if (isset($meta_array['_psc_stock_status_simple']) && $meta_array['_psc_stock_status_simple'] == 'instock') {
                    if (strlen($product_stock) > 0) {
                        if ($product_stock > 0) {
                            $result = $product_stock;
                        } else {
                            $result = 'outofstock';
                        }
                    } else {
                        $result = 'instock';
                    }
                } else if (isset($meta_array['_psc_stock_status_simple']) && $meta_array['_psc_stock_status_simple'] == 'outofstock') {
                    $result = 'outofstock';
                }
            } else {
                $result = 'instock';
            }
        } else if ($meta_array['psc-product-type-dropdown'] == 'variable') {
//            $psc_variable_product_data = unserialize(trim($meta_array['psc_variable_product_data']));
//            $psc_variable_product_data = unserialize($psc_variable_product_data);            
              $psc_variable_product_data = $this->get_unserialize_data($meta_array['psc_variable_product_data']);
              
            if (is_array($psc_variable_product_data) && count($psc_variable_product_data) > 0) {
                foreach ($psc_variable_product_data as $key => $value) {
                    if ($pname == $value['psc_variable_product_name' . $key]) {
                        if ('1' == $value['_psc_manage_stock_variable' . $key]) {
                            $product_stock_variablle = trim($value['psc_variable_product_stock' . $key]);
                            if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == 1) {
                                if (strlen($product_stock_variablle) > 0) {
                                    if ($product_stock_variablle > 0) {
                                        $result = $product_stock_variablle;
                                    } else {
                                        $result = 'outofstock';
                                    }
                                } else {
                                    $result = 'instock';
                                }
                            } else if (isset($value['psc_variable_product_stock_status' . $key]) && $value['psc_variable_product_stock_status' . $key] == 0) {
                                $result = 'outofstock';
                            }
                            return $result;
                        } else {
                            return 'instock';
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function psc_product_price_empty($post_id) {

        $result = false;
        $meta_array = $this->get_post_meta_all($post_id);
        if (isset($meta_array['psc-product-type-dropdown']) && $meta_array['psc-product-type-dropdown'] == 'simple') {
            if (isset($meta_array['_psc_regular_price']) && $meta_array['_psc_regular_price'] != 0 && $meta_array['_psc_regular_price'] > 0) {
                $result = true;
            }
        } else if (isset($meta_array['psc-product-type-dropdown']) && $meta_array['psc-product-type-dropdown'] == 'variable') {
            //$psc_variable_product_data = unserialize(trim($meta_array['psc_variable_product_data']));
           // $psc_variable_product_data = unserialize($psc_variable_product_data);
            $psc_variable_product_data = $this->get_unserialize_data($meta_array['psc_variable_product_data']);
            
            if (is_array($psc_variable_product_data) && count($psc_variable_product_data) > 0) {
                foreach ($psc_variable_product_data as $key => $value) {
                    if ($value['psc_variable_product_regular_price' . $key] != 0 && $value['psc_variable_product_regular_price' . $key] > 0) {
                        $result = true;
                        break;
                    }
                }
            }
        }
        return $result;
    }    

    public function enable_paypal_express_checkout_button() {
        $result = false;
        $psc_pec_enabled = (get_option('psc_pec_enabled')) ? get_option('psc_pec_enabled') : 'no';
        if ($psc_pec_enabled == 'yes') {
            $result = $this->get_credentials_payment_mode();
        }
        return $result;
    }

    public function get_credentials_payment_mode() {

        $result = false;
        $api_username = "";
        $api_password = "";
        $api_signature = "";
        $is_testmode = (get_option('psc_pec_testmode') == 'yes' ) ? true : false;
        if ($is_testmode) {
            $api_username = (get_option('psc_pec_sandbox_api_username')) ? get_option('psc_pec_sandbox_api_username') : '';
            $api_password = (get_option('psc_pec_sandbox_api_password')) ? get_option('psc_pec_sandbox_api_password') : '';
            $api_signature = (get_option('psc_pec_sandbox_api_signature')) ? get_option('psc_pec_sandbox_api_signature') : '';
        } else {
            $api_username = (get_option('psc_pec_api_username')) ? get_option('psc_pec_api_username') : '';
            $api_password = (get_option('psc_pec_api_password')) ? get_option('psc_pec_api_password') : '';
            $api_signature = (get_option('psc_pec_api_signature')) ? get_option('psc_pec_api_signature') : '';
        }
        $result = $this->credentials_is_empty($api_username, $api_password, $api_signature);
        return $result;
    }

    public function credentials_is_empty($api_username, $api_password, $api_signature) {

        $result = false;
        if ((isset($api_username) && !empty($api_username)) && (isset($api_password) && !empty($api_password)) && (isset($api_signature) && !empty($api_signature))) {
            $result = true;
        }
        return $result;
    }

    public function get_all_order_note_by_post_id($post_id) {
        try {
            $args = array('post_id' => $post_id);
            $comments = get_comments($args);
            $result_string = "";
            foreach ($comments as $comment) {
                if ('0' == $comment->comment_approved) {
                    $iscustomer_note = $this->is_note_to_customer($comment->comment_ID);
                    $customer_bg_color = "background: #efefef";
                    $date_time_value = explode(" ", $comment->comment_date);
                    $insert_date = isset($date_time_value[0]) ? $date_time_value[0] : '';
                    $insert_time = isset($date_time_value[1]) ? $date_time_value[1] : '';
                    $is_private_or_customer = "is_private";
                    if ($iscustomer_note) {
                        $is_private_or_customer = "is_cuatomer";
                        $customer_bg_color = "background: #d7cad2";
                    }
                    $result_string .='<div class="psc_dinamic_add_display_note"><div class="private ' . $is_private_or_customer . '" style="' . $customer_bg_color . '"><span>' . $comment->comment_content . '</span><br/></div><span class="psc_commenter">' . __('added on', 'pal-shopping-cart') . ' ' . date('F j, Y', strtotime($insert_date)) . ' ' . __('at', 'pal-shopping-cart') . ' ' . strtolower(date('H:i A', strtotime($insert_time))) . ' ' . __('by', 'pal-shopping-cart') . ' ' . $comment->comment_author . '</span><br /><span class="psc_commenter_delete_comments"><a href="javascript:;" class="psc_delete" data-comment-id="' . $comment->comment_ID . '">' . __('Delete Notes', 'pal-shopping-cart') . '</a></span></div>';
                }
            }
            return $result_string;
        } catch (Exception $ex) {
            
        }
    }

    public function is_note_to_customer($comment_ID) {
        try {
            $result = false;
            $is_customer_note = get_comment_meta($comment_ID, 'is_customer_note');
            if (isset($is_customer_note[0]) && '1' == $is_customer_note[0]) {
                $result = true;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public function create_paypal_shopping_cart_page() {

        $psc_shop_page_id = $this->get_psc_page_id_by_page_tital('Shop');
        $psc_cart_page_id = $this->get_psc_page_id_by_page_tital('Cart');
        $psc_checkout_page_id = $this->get_psc_page_id_by_page_tital('Checkout');
        $psc_review_order = $this->get_psc_page_id_by_page_tital('Review Order');
        $psc_order_received = $this->get_psc_page_id_by_page_tital('Order Received');

        if (isset($psc_shop_page_id)) {
            $is_avilable_shop_page = $this->get_page_status_by_postid($psc_shop_page_id);
            if ($is_avilable_shop_page == false) {
                $post = array(
                    'ping_status' => 'closed',                    
                    'post_name' => 'Shop',
                    'post_status' => 'publish',
                    'post_title' => 'Shop',
                    'post_type' => 'page',
                    'post_content' => '[psc_shop]',
                    'comment_status' => 'closed'
                );
                $newvalue = wp_insert_post($post, false);
                update_option('psc_shoppage_product_settings', $newvalue);
            }
        }
        if (isset($psc_cart_page_id)) {

            $is_avilable_cart_page = $this->get_page_status_by_postid($psc_cart_page_id);
            if ($is_avilable_cart_page == false) {
                $post = array(
                    'ping_status' => 'closed',
                    'post_name' => 'Cart',
                    'post_status' => 'publish',
                    'post_title' => 'Cart',
                    'post_type' => 'page',
                    'post_content' => '[psc_cart]',
                    'comment_status' => 'closed'
                );
                $newvalue = wp_insert_post($post, false);
                update_option('psc_cartpage_product_settings', $newvalue);
            }
        }
        if (isset($psc_checkout_page_id)) {

            $is_avilable_checkout_page = $this->get_page_status_by_postid($psc_checkout_page_id);
            if ($is_avilable_checkout_page == false) {
                $post = array(
                    'ping_status' => 'closed',
                    'post_name' => 'Checkout',
                    'post_status' => 'publish',
                    'post_title' => 'Checkout',
                    'post_type' => 'page',
                    'post_content' => '[psc_checkout]',
                    'comment_status' => 'closed'
                );
                $newvalue = wp_insert_post($post, false);
                update_option('psc_checkoutpage_product_settings', $newvalue);
            }
        }
        if (isset($psc_review_order)) {

            $is_avilable_checkout_page = $this->get_page_status_by_postid($psc_review_order);
            if ($is_avilable_checkout_page == false) {
                $post = array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_name' => 'Review Order',
                    'post_status' => 'publish',
                    'post_title' => 'Review Order',
                    'post_type' => 'page',
                    'post_content' => '[psc_review_order]'
                );
                $newvalue = wp_insert_post($post, false);
            }
        }
        if (isset($psc_order_received)) {

            $is_avilable_checkout_page = $this->get_page_status_by_postid($psc_order_received);
            if ($is_avilable_checkout_page == false) {
                $post = array(
                    'ping_status' => 'closed',
                    'post_name' => 'Order Received',
                    'post_status' => 'publish',
                    'post_title' => 'Order Received',
                    'post_type' => 'page',
                    'post_content' => '[psc_order_received]',
                    'comment_status' => 'closed'
                );
                $newvalue = wp_insert_post($post, false);
            }
        }

        return;
    }

    public function get_psc_page_id_by_page_tital($title) {
        $result = '';
        $result = get_page_by_title($title);
        $result = isset($result->ID) ? $result->ID : '';
        return $result;
    }

    public function get_page_status_by_postid($id) {
        global $wpdb;
        $is_avialble_page = false;
        if (isset($id) && !empty($id)) {
            $result = $wpdb->get_results("SELECT * FROM `" . $wpdb->posts . "` WHERE `ID` = " . $id . " and `post_status` = 'publish'");

            if (is_array($result) && count($result) > 0) {
                if ($result[0]->post_status == 'publish') {
                    $is_avialble_page = true;
                }
            }
        }

        return $is_avialble_page;
    }

    public function update_cart_coupon_in_array(){
        
        $result = array();        
        $coupon_cart_discount_unset = $this->session_get('coupon_cart_discount_array');
        $total_cart = $this->session_cart_contents_total('cart_total');
        $is_total_amount = 0;
        $is_total_temp = 0;
        $my_discount_total = 0;
        if( is_array($coupon_cart_discount_unset) && count($coupon_cart_discount_unset) > 0 ){
            foreach ($coupon_cart_discount_unset as $key => $value) {
                $postmeta_data = $this->get_post_meta_all($value['post_id']);
                if( (isset($total_cart) && !empty($total_cart)) && (isset($value['psc_coupon_amount']) && $value['psc_coupon_amount'] > 0 ) ){
                    $is_total_amount = $is_total_amount + $postmeta_data['psc_coupon_amount'];                 
                    if( $total_cart > $is_total_amount  ){
                        $value['psc_coupon_amount'] = $postmeta_data['psc_coupon_amount'];
                        $result[$key] = $value;
                        $is_total_temp = $is_total_temp + $is_total_amount;
                        $my_discount_total = $my_discount_total + $postmeta_data['psc_coupon_amount'];
                    } else {
                        $is_total_amount_temp = $total_cart - $is_total_temp;
                        $value['psc_coupon_amount'] = $is_total_amount_temp;  
                        $my_discount_total = $my_discount_total + $is_total_amount_temp;
                        $result[$key] = $value;
                    }                 
                }                            
            }             
        } 
         $this->session_set('cart_total_discount', $my_discount_total);
        $this->session_set('coupon_cart_discount_array', $result);
       // return $result;
    }
    
    public function remove_cart_coupon_in_array($is_remove_coupon_enable = NULL){
        
        $result = array();
        
        $coupon_cart_discount_unset = $this->session_get('coupon_cart_discount_array');        
        if( is_array($coupon_cart_discount_unset) && count($coupon_cart_discount_unset) > 0 ){
            foreach ($coupon_cart_discount_unset as $key => $value) {
                if (strtolower($key) == strtolower($is_remove_coupon_enable) ) {
                    
                    $psc_coupon_amount = ($value['psc_coupon_amount'])?$value['psc_coupon_amount']:0;
                    $total_cart = $this->psc_get_cart_total_discount();
                    if( isset($total_cart) && $total_cart != 0 ){
                        $total_cart = $total_cart - $psc_coupon_amount;
                    }   
                    
                    $this->session_set('cart_total_discount', $total_cart);
                    unset($coupon_cart_discount_unset[strtolower($is_remove_coupon_enable)]);
                    $coupon_cart_discount_unset = $this->psc_remove_coupon_after_add( $coupon_cart_discount_unset );                   
                    break;
                } 
            }
             $result = $coupon_cart_discount_unset;
        } 
        $this->session_set('coupon_cart_discount_array', $result);
        return $result;
    }

    public function psc_get_cart_total_discount(){
        
        $result = 0;        
        $result_total = ($this->session_get('cart_total_discount'))?$this->session_get('cart_total_discount'):0;  
        if( isset($result_total) && !empty($result_total) ){
            $result = $result_total;
        }
        return $result;
    }
    
    public function psc_remove_coupon_after_add( $coupon_cart_discount_unset ){
        $result = array();
        
        $cart_total = $this->obj_cart_heandler->_cart_contents['cart_total'];
        $cart_total_discount = 0;
 
        foreach ($coupon_cart_discount_unset as $key => $value) {
            
            $postmeta_data = $this->get_post_meta_all($value['post_id']); 
            
            if( $postmeta_data['psc_coupon_amount'] > $value['psc_coupon_amount'] ){
                $pay_amount = $postmeta_data['psc_coupon_amount'] - $value['psc_coupon_amount'];               
                $value['psc_coupon_amount'] = $pay_amount + $value['psc_coupon_amount'];
            }            
            $result[$key] = $value; 
            $cart_total_discount = $cart_total_discount + $value['psc_coupon_amount'];
        }
        
        $this->session_set('cart_total_discount',$cart_total_discount); 
        
        return $result;
    }
    
    public function get_coupon_discount_and_code( ){
        $result = array();
        $result_array = ($this->session_get('coupon_cart_discount_array'))?$this->session_get('coupon_cart_discount_array'):'';
        
        if(is_array($result_array) && count($result_array) > 0 ){
            $result = $result_array;
        }        
        return $result;
    }
    
    public function get_cart_total_is_empty(){
        $is_cart = $this->session_cart_contents_total('cart_total');
        $is_discount = $this->psc_get_cart_total_discount();
        $is_zero = 0;
        $result = TRUE;
        if( (isset($is_cart) && $is_cart > 0) && (isset($is_discount) && $is_discount > 0) ){            
            $is_zero = $is_cart - $is_discount;
            if( $is_zero == 0 ){
                $result = FALSE;
            }
        }
        return $result;
    }

    public function create_order() {

        $newid = "";
        $post = array(
            'comment_status' => 'open',
            'ping_status' => 'closed',
            'post_name' => 'psc_order',
            'post_status' => 'publish',
            'post_title' => 'psc_order',
            'post_type' => 'psc_order',
            'post_content' => '',
        );
        $newid = wp_insert_post($post, false);
        return $newid;
    }

    public function pac_stock_management_store() {

        $item_array_name = $this->session_cart_contents();

        foreach ($item_array_name as $value) {
            $product_name = $value['name'];
            $product_qty = $value['qty'];
            $product_id = preg_replace("/_[^*]+/", '', $value['id']);

            if ((isset($product_name) && !empty($product_name)) && (isset($product_qty) && !empty($product_qty)) && (isset($product_id) && !empty($product_id))) {
                $result_array = $this->get_post_meta_all($product_id);
                if (is_array($result_array)) {
                    if (isset($result_array['psc-product-type-dropdown']) && $result_array['psc-product-type-dropdown'] == "simple") {

                        if (isset($result_array['_psc_stock_qty_simple']) && !empty($result_array['_psc_stock_qty_simple']) && $result_array['_psc_stock_qty_simple'] != '0') {
                            $qty_simple = $result_array['_psc_stock_qty_simple'] - $product_qty;
                        } else {
                            if (isset($result_array['_psc_stock_qty_simple']) && $result_array['_psc_stock_qty_simple'] == '0') {
                                $qty_simple = '0';
                            } else {
                                $qty_simple = '';
                            }
                        }

                        update_post_meta($product_id, '_psc_stock_qty_simple', $qty_simple);
                    } else if (isset($result_array['psc-product-type-dropdown']) && $result_array['psc-product-type-dropdown'] == "variable") {
                        $expload_data = explode(':', $product_name);
                        $result_unsirealize = $this->get_unserialize_data($result_array['psc_variable_product_data']);
                        foreach ($result_unsirealize as $key => $value) {
                            if ($expload_data[1] == $value['psc_variable_product_name' . $key]) {


                                if (isset($value['psc_variable_product_stock' . $key]) && !empty($value['psc_variable_product_stock' . $key]) && $value['psc_variable_product_stock' . $key] != '0') {
                                    $qty_variable = $value['psc_variable_product_stock' . $key] - $product_qty;
                                } else {
                                    if (isset($value['psc_variable_product_stock' . $key]) && $value['psc_variable_product_stock' . $key] == '0') {
                                        $qty_variable = '0';
                                    } else {
                                        $qty_variable = '';
                                    }
                                }

                                $result_unsirealize[$key]['psc_variable_product_stock' . $key] = $qty_variable;
                            }
                        }
                        update_post_meta($product_id, 'psc_variable_product_data', serialize($result_unsirealize));
                    }
                }
            }
        }
    }

    public function update_payment_status_by_paypal_responce_comment_table($order_note) {

        $time = current_time('mysql');
        $current_user = wp_get_current_user();
        $order = $this->session_get('order_id');
        $result = array(
            'comment_post_ID' => isset($order) ? $order : '',
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

    public function psc_calculate_cart_totals() {
        $total_cart = '';
        $total_cart = ($this->session_cart_contents_total('cart_total'))?$this->session_cart_contents_total('cart_total'):0;
        return $total_cart;
    }

    public function psc_get_order_tax() {
        try {
            $result = 0;
            $cart_tax = $this->obj_cart_heandler->contents();
            foreach ($cart_tax as $key => $value) {

                if (isset($value['tax']) && !empty($value['tax'])) {
                    $qty_tax = $value['qty'] * $value['tax'];
                    $result = $result + $qty_tax;
                }
            }
            return number_format($result, 2, '.', '');
        } catch (Exception $ex) {
            
        }
    }
    
    public function psc_get_order_shipping() {
        try {
            $result = 0;
            $cart_ship = $this->obj_cart_heandler->contents();
            foreach ($cart_ship as $key => $value) {

                if (isset($value['shipping']) && !empty($value['shipping'])) {
                    $qty_ship = $value['qty'] * $value['shipping'];                    
                    $result = $result + $qty_ship;
                }
            }
            return number_format($result, 2, '.', '');
        } catch (Exception $ex) {
            
        }
    }
    
    public function psc_get_order_total_with_tax_and_ship() {
        try {
            $result = 0;
            $total_cart = ($this->psc_calculate_cart_totals())?$this->psc_calculate_cart_totals():0;
            $total_cart_tax = ($this->psc_get_order_tax())?$this->psc_get_order_tax():0;
            $total_cart_ship = ($this->psc_get_order_shipping())?$this->psc_get_order_shipping():0;
            $total_cart_discount = ($this->session_get('cart_total_discount'))?$this->session_get('cart_total_discount'):0;
            
            if (isset($total_cart_discount) && !empty($total_cart_discount)) {
                 $result = ($total_cart + $total_cart_tax + $total_cart_ship) - $total_cart_discount;
            } else {
                $result = $total_cart + $total_cart_tax + $total_cart_ship;
            }
            
            return number_format($result, 2, '.', '');
        } catch (Exception $ex) {
            
        }
    }

    public function currency_has_decimals($currency) {
        if (in_array($currency, array('HUF', 'JPY', 'TWD'))) {
            return false;
        }

        return true;
    }

    public function round($price, $currency) {
        $precision = 2;

        if (!$this->currency_has_decimals($currency)) {
            $precision = 0;
        }

        return round($price, $precision);
    }

    public function number_format($price, $currency) {
        $decimals = 2;

        if (!$this->currency_has_decimals($currency)) {
            $decimals = 0;
        }

        return number_format($price, $decimals, '.', '');
    }

}