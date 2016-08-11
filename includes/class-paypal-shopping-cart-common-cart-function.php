<?php

class PSC_Common_Cart_Function {

    public function __construct() {

        $this->obj_session_heandler = new PSC_Session_Handler();
        $this->obj_cart_heandler = new PSC_Cart_Handler();
        $this->PSC_Common_Function = new PSC_Common_Function();
        $this->currency = $this->PSC_Common_Function->get_psc_currency();
        $this->cart_contents = $this->obj_cart_heandler->contents();
    }

    public function psc_get_cart_sub_total() {
        try {

            $sub_total = $this->obj_cart_heandler->_cart_contents['cart_total'];

            return ( $sub_total ) ? $this->PSC_Common_Function->number_format($sub_total, $this->currency) : 0;
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_total() {
        try {

            $sub_total = $this->psc_get_cart_sub_total();
            $tax_total = $this->psc_get_cart_tax_total();
            $shipping_total = $this->psc_get_cart_shipping_total();
            $discount_total = $this->psc_get_cart_discount_total();
            $total = ( $sub_total + $tax_total + $shipping_total ) - $discount_total;

            return ( $total ) ? $this->PSC_Common_Function->number_format($total, $this->currency) : 0;
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_tax_total() {
        try {

            $tax_total = 0;
            
            if (!is_array($this->cart_contents) && count($this->cart_contents) == 0) {
                return $tax_total;
            }

            foreach ($this->cart_contents as $key => $value) {
                $tax_total = $tax_total + $value['tax'];
            }

            return $this->PSC_Common_Function->number_format($tax_total, $this->currency);
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_shipping_total() {
        try {

            $shipping_total = 0;
            
            if (!is_array($this->cart_contents) && count($this->cart_contents) == 0) {
                return $shipping_total;
            }

            foreach ($this->cart_contents as $key => $value) {
                $shipping_total = $shipping_total + $value['shipping'];
            }

            return $this->PSC_Common_Function->number_format($shipping_total, $this->currency);
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_shipping() {
        try {

            $shipping_total = 0;
            $result = array();
            
            if (!is_array($this->cart_contents) && count($this->cart_contents) == 0) {
                return $result;
            }

            foreach ($this->cart_contents as $key => $value) {
                $shipping_total = $shipping_total + $value['shipping'];
            }

            if (isset($shipping_total) && !empty($shipping_total)) {

                $result['name'] = 'Shipping: ';
                $result['amt'] = $this->PSC_Common_Function->number_format($shipping_total, $this->currency);
                $result['qty'] = '1';
            }
            return $result;
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_discount_total() {
        try {
            
            $discount_amount = 0;
            $discount_array = $this->obj_session_heandler->userdata('coupon_cart_discount_array');
            if ($discount_array == false) {
                return $discount_amount;
            }

            foreach ($discount_array as $key => $value) {
                $discount_amount = $discount_amount + $value['psc_coupon_amount'];
            }

            return $this->PSC_Common_Function->number_format($discount_amount, $this->currency);
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_discount() {
        try {

            $coupon_result = array();
            $coupon_array = $this->obj_session_heandler->userdata('coupon_cart_discount_array');

            if (!is_array($coupon_array) && count($coupon_array) == 0) {
                return $coupon_result;
            }
            
            foreach ($coupon_array as $key => $value) {  
                $coupon_result_tamp = array();
                $coupon_result_tamp['name'] = 'Discount Code: ' . $value['coupon_code'];
                $coupon_result_tamp['amt'] = '-' . $this->PSC_Common_Function->number_format($value['psc_coupon_amount'], $this->currency);
                $coupon_result_tamp['number'] = $value['coupon_code'];
                $coupon_result_tamp['qty'] = '1';                 
                array_push($coupon_result, $coupon_result_tamp);
            }
            
            return $coupon_result;
            
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_item_total() {
        try {

            $item_total = 0;
            
            if (!is_array($this->cart_contents) && count($this->cart_contents) == 0) {
                return $item_total;
            }

            foreach ($this->cart_contents as $key => $value) {
                $item_total = $item_total + ( $value['price'] * $value['qty'] );
            }

            return $this->PSC_Common_Function->number_format($item_total - $this->psc_get_cart_discount_total(), $this->currency);
        } catch (Exception $e) {
            
        }
    }

    public function psc_get_cart_item() {
        try {

            $cart_items = array();
            
            if (!is_array($this->cart_contents) && count($this->cart_contents) == 0) {
                return $cart_items;
            }
            
            foreach ($this->cart_contents as $key => $item) {
                $Item = array(
                    'name' => substr($item['name'], 0, 127),                    
                    'amt' => $item['price'],                    
                    'qty' => $item['qty']                    
                );
                array_push($cart_items, $Item);
            }
            
            $discount_line_item = $this->psc_get_cart_discount();
            
            if(is_array($discount_line_item) && count($discount_line_item) > 0 ){
                
                foreach ($discount_line_item as $key => $value) {
                    array_push($cart_items, $value);
                }
            }
            
            return $cart_items;
            
        } catch (Exception $e) {
            
        }
    }

}
