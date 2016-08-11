<?php

class PSC_Product_Save_Data {

    public function save_new_product_data_postmeta($post_id) {
        $product_data = $_REQUEST;
        if (get_post_type() != 'psc_product') {
            return FALSE;
        }
        if (wp_is_post_revision($post_id)) {
            return;
        }
        $product_data_key = array();
        $variable_product_array = "";
        if (isset($product_data['psc-product-type-dropdown']) && $product_data['psc-product-type-dropdown'] == 'simple') {


            if (isset($product_data['_psc_regular_price']) && !empty($product_data['_psc_regular_price'])) {

                $product_data_key = array(
                    "psc-product-type-dropdown" => "",
                    "_psc_sku" => "",
                    "_psc_regular_price" => "",
                    "_psc_sale_price" => "",
                    "psc_noncename" => "",
                    "_wp_editor_test_1" => "",
                    "post_name" => "",
                    "_psc_manage_stock_simple" => "",
                    "_psc_stock_qty_simple" => "",
                    "_psc_sale_tax" => "",
                    "_psc_sale_ship" => "",
                    "_psc_stock_status_simple" => ""
                );

                if (!array_key_exists('_psc_manage_stock_simple', $product_data)) {
                    $product_data['_psc_manage_stock_simple'] = 'no';
                }
            }
        } elseif (isset($product_data['psc-product-type-dropdown']) && $product_data['psc-product-type-dropdown'] == 'variable') {
            $product_data_key = array(
                "psc-product-type-dropdown" => "",
                "_psc_sku" => "",
                "psc_noncename" => "",
                "_wp_editor_test_1" => "",
                "post_name" => "",
                "psc_variable_product_count" => ""
            );
            $variable_product_array = serialize($this->psc_get_variable_product($product_data));
        }

        if (isset($product_data) && !empty($product_data)) {
            foreach ($product_data as $key => $value) {
                if (array_key_exists($key, $product_data_key)) {


                    if ($key == 'psc_variable_product_count') {
                        update_post_meta($post_id, 'psc_variable_product_count', $value);
                        update_post_meta($post_id, 'psc_variable_product_data', $variable_product_array);
                    } else {
                        update_post_meta($post_id, $key, $value);
                    }
                }
            }
        }
    }

    public function psc_get_variable_product($product_data) {

        $variable_product_array = array();

        if (array_key_exists('psc_variable_product_count', $product_data)) {
            $j = 0;
            for ($i = 0; $i <= $product_data['psc_variable_product_count']; $i++) {
                $array_manage = array();

                if ($this->insert_variable_product_status($product_data, $i)) {

                    $array_manage['psc_variable_product_name' . $j] = isset($product_data['psc_variable_product_name' . $i]) ? $product_data['psc_variable_product_name' . $i] : '';
                    $array_manage['psc_variable_product_regular_price' . $j] = isset($product_data['psc_variable_product_regular_price' . $i]) ? $product_data['psc_variable_product_regular_price' . $i] : 0;
                    $array_manage['psc_variable_product_sale_price' . $j] = isset($product_data['psc_variable_product_sale_price' . $i]) ? $product_data['psc_variable_product_sale_price' . $i] : 0;
                    $array_manage['_psc_manage_stock_variable' . $j] = isset($product_data['_psc_manage_stock_variable' . $i]) ? $product_data['_psc_manage_stock_variable' . $i] : 0;
                    $array_manage['psc_variable_product_stock' . $j] = isset($product_data['psc_variable_product_stock' . $i]) ? $product_data['psc_variable_product_stock' . $i] : 0;
                    $array_manage['psc_variable_product_tax' . $j] = isset($product_data['psc_variable_product_tax' . $i]) ? $product_data['psc_variable_product_tax' . $i] : 0;
                    $array_manage['psc_variable_product_ship' . $j] = isset($product_data['psc_variable_product_ship' . $i]) ? $product_data['psc_variable_product_ship' . $i] : 0;
                    $array_manage['psc_variable_product_stock_status' . $j] = isset($product_data['psc_variable_product_stock_status' . $i]) ? $product_data['psc_variable_product_stock_status' . $i] : 0;

                    array_push($variable_product_array, $array_manage);
                    $j++;
                }
            }
        }

        return $variable_product_array;
    }

    public function insert_variable_product_status($product_data, $i) {
        $result = false;

        if ((isset($product_data['psc_variable_product_name' . $i])) && !empty($product_data['psc_variable_product_name' . $i])) {
            if ((isset($product_data['psc_variable_product_regular_price' . $i])) && !empty($product_data['psc_variable_product_regular_price' . $i])) {
                $result = true;
            }
        }


        return $result;
    }

    public function save_new_coupons_data_postmeta($post_id) {
        $coupons_array = $_REQUEST;
        $coupons_array_key = array(
            "post_title" => "",
            "excerpt" => "",
            "psc_coupon_discount_type" => "",
            "psc_coupon_amount" => "",
            "psc_coupon_expiry_date" => ""
        );

        if( !isset($coupons_array['psc_coupon_amount']) || $coupons_array['psc_coupon_amount'] <= 0 ){             
            return;
        }
        
        if (isset($coupons_array) && !empty($coupons_array)) {
            foreach ($coupons_array as $key => $value) {
                if (array_key_exists($key, $coupons_array_key)) {
                    update_post_meta($post_id, $key, $value);
                }
            }
        }
    }
}

?>