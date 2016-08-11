<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div>
    <h1 style="color:#77a464"><?php echo esc_html('Thank you. Your order has been received.') ?></h1>
    <div class="psc-received">
        <?php
        $order = '';
        $PSC_Common_Function = new PSC_Common_Function();
        $currency_code = $PSC_Common_Function->get_psc_currency();
        $currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
        $coupon_cart_discount = $PSC_Common_Function->get_cart_total_discount();
        $coupon_code = $PSC_Common_Function->get_cart_total_coupon_code();
        $is_psc_tax = 0;
        $is_psc_ship = 0;
        if (isset($_GET['psc_action']) && $_GET['psc_action'] == 'order_received') {
            $order = sanitize_text_field($_GET['order']);
            $get_result_of_order_received_data = $PSC_Common_Function->get_result_of_order_received_data($order);
            if (is_array($get_result_of_order_received_data) && count($get_result_of_order_received_data) > 0) {
               
                $psc_cart_serialize = array();
                if (isset($get_result_of_order_received_data['_psc_cart_serialize']) && !empty($get_result_of_order_received_data['_psc_cart_serialize'])) {
//                    $psc_serialize = unserialize(trim($get_result_of_order_received_data['_psc_cart_serialize']));
//                    $psc_cart_serialize = unserialize($psc_serialize);
                    $psc_cart_serialize = $PSC_Common_Function->get_unserialize_data($get_result_of_order_received_data['_psc_cart_serialize']);
                }
                
                $coupon_cart_discount_array = array();
                if (isset($get_result_of_order_received_data['_order_cart_discount']) && !empty($get_result_of_order_received_data['_order_cart_discount'])) {
//                $order_cart_discount = unserialize(trim($get_result_of_order_received_data['_order_cart_discount']));
//                $coupon_cart_discount_array = unserialize($order_cart_discount);
                $coupon_cart_discount_array = $PSC_Common_Function->get_unserialize_data($get_result_of_order_received_data['_order_cart_discount']);
                }
                
                
                ?>
                <ul class="psc-thankyou-order-details">
                    <li class="order">
                        <?php echo __('Order Number', 'pal-shopping-cart') ?>: <strong><?php echo esc_html($get_result_of_order_received_data[0]['_orderid']); ?></strong>
                    </li>
                    <li class="date">
                        <?php echo __('Date', 'pal-shopping-cart') ?>: <strong><?php echo esc_html($get_result_of_order_received_data[0]['_orderdate']); ?></strong>
                    </li>
                    <li class="total">
                        <?php echo __('Total', 'pal-shopping-cart') ?>: <strong><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($get_result_of_order_received_data['_psc_cart_total']), 2, '.', ''); ?></span></strong>
                    </li>
                    <li class="method">
                        <?php echo __('Payment Method', 'pal-shopping-cart') ?>: <strong><?php echo esc_html($get_result_of_order_received_data['_payment_method_title']); ?></strong>
                    </li>
                </ul>
                <div class="clear"></div>
                <h1><?php echo __('Order Details', 'pal-shopping-cart') ?>: </h1>
                <table class="psc-checkout-review-order-table">
                    <thead>
                        <tr>
                            <th class="psc-product-name"><?php echo __('Product', 'pal-shopping-cart') ?></th>
                            <th class="psc-product-total"><?php echo __('Total', 'pal-shopping-cart') ?></th>
                        </tr>
                    </thead>
                    <tbody> 

                        <?php foreach ($psc_cart_serialize as $key => $value) {                                                     
                            
                            $qty_tax = $value['qty'] * $value['tax'];
                            $is_psc_tax = $is_psc_tax + $qty_tax;
                            
                            $qty_ship = $value['qty'] * $value['shipping'];
                            $is_psc_ship = $is_psc_ship + $qty_ship;
                            
                            ?>  
                            <tr class="psc_cart_item">
                                <td class="psc_product-name">
                                    <strong class="psc_product-quantity"><a href="<?php echo get_permalink($value['id']);?>"><?php echo esc_html($value['name']) . '</a> × ' . esc_html($value['qty']); ?></strong>	
                                </td>
                                <td class="psc_product-total">
                                    <span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($value['subtotal']), 2); ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="cart-subtotal">
                            <th><?php echo __('SUBTOTAL', 'pal-shopping-cart') ?>: </th>
                            <td>
                                <span class="amount">
                                    <?php echo esc_html($currency_symbol) . '' . number_format(esc_html($get_result_of_order_received_data['_psc_cart_subtotal']), 2, '.', ''); ?>
                                </span>
                            </td>
                        </tr>
                        <?php
                    if (is_array($coupon_cart_discount_array) && count($coupon_cart_discount_array) > 0) {
                        foreach ($coupon_cart_discount_array as $key => $value) {                          
                            $total_amount_pay = '-' . esc_html($currency_symbol) . '' . number_format(esc_html(str_replace('-', '', $value['psc_coupon_amount'])), 2);
                                        if( $value['psc_coupon_amount'] == 0 ){
                                            $total_amount_pay = "";
                                        }
                            ?>
                            <tr class="cart-coupons">  
                                <th><?php _e('Coupons', 'pal-shopping-crt') ?>: <?php echo esc_html($value['coupon_code']); ?></th>
                                <td><span class="amount"><?php echo $total_amount_pay; ?></span></td>
                            </td>
                        </tr>
                            <?php
                        }
                    }
                    
                    
                    if( isset($is_psc_tax) && !empty($is_psc_tax) ){
                        ?>
                        <tr class="cart-order-tax">
                            <th><?php echo __('TAX ', 'pal-shopping-cart') ?>: </th>
                            <td>
                                <span class="order-tax"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_tax), 2, '.', ''); ?></span>
                            </td>
                        </tr>    
                        <?php
                    }
                    
                    if( isset($is_psc_ship) && !empty($is_psc_ship) ){
                    ?>                    
                        <tr class="cart-order-ship">
                            <th><?php echo __('Shipping ', 'pal-shopping-cart') ?>: </th>
                            <td>
                                <span class="order-ship"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_ship), 2, '.', ''); ?></span>
                            </td>
                        </tr>    
                        <?php
                    }
                    
                    ?>
                        
                        <tr class="cart-payment-method">
                            <th><?php echo __('PAYMENT METHOD', 'pal-shopping-cart') ?>: </th>
                            <td>
                                <span class="payment-method"><?php echo esc_html($get_result_of_order_received_data['_payment_method_title']); ?></span>
                            </td>
                        </tr>
                        <tr class="order-total">
                            <th><?php echo __('TOTAL', 'pal-shopping-cart') ?>: </th>
                            <td>
                                <strong><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($get_result_of_order_received_data['_psc_cart_total']), 2, '.', ''); ?></span></strong> 
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="clear"></div>
                <h1><?php echo __('Customer Details', 'pal-shopping-cart') ?>: </h1>
                <table class="psc-checkout-review-order-table">                    
                    <tbody>                         

                        <?php if (isset($get_result_of_order_received_data['_billing_email']) && !empty($get_result_of_order_received_data['_billing_email'])) { ?>
                            <tr class="psc_cart_item">
                                <th class="psc-product-name"><?php echo __('EMAIL', 'pal-shopping-cart') ?>: </th>
                                <td class="psc_product-total">
                                    <span class="amount"><?php echo esc_html($get_result_of_order_received_data['_billing_email']); ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (isset($get_result_of_order_received_data['_billing_phone']) && !empty($get_result_of_order_received_data['_billing_phone'])) { ?>
                            <tr class="cart-subtotal">
                                <th class="psc-product-name"><?php echo __('TELEPHONE', 'pal-shopping-cart') ?>: </th>
                                <td>
                                    <span class="amount"><?php echo esc_html($get_result_of_order_received_data['_billing_phone']); ?></span>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>        
                <div class="psc-col2-set addresses">
                    <div class="psc-col-1">
                        <header class="title">
                            <h3><?php echo __('Billing Address', 'pal-shopping-cart') ?></h3>
                        </header>
                        <address>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_company']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_full_name']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_city']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_address_1']) . '' . esc_html($get_result_of_order_received_data['_shipping_address_2']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_city']) . esc_html('-') . esc_html($get_result_of_order_received_data['_shipping_postcode']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_state']) . esc_html(', ') . esc_html($PSC_Common_Function->two_digit_get_countrycode_to_country($get_result_of_order_received_data['_shipping_country'])); ?>                           
                        </address>
                    </div><!-- /.col-1 -->
                    <div class="psc-col-2">
                        <header class="title">
                            <h3><?php echo __('Shipping Address', 'pal-shopping-cart') ?></h3>
                        </header>
                        <address>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_company']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_full_name']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_city']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_address_1']) . '' . esc_html($get_result_of_order_received_data['_shipping_address_2']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_city']) . esc_html('-') . esc_html($get_result_of_order_received_data['_shipping_postcode']); ?><br>
                            <?php echo esc_html($get_result_of_order_received_data['_shipping_state']) . esc_html(', ') . esc_html($PSC_Common_Function->two_digit_get_countrycode_to_country($get_result_of_order_received_data['_shipping_country'])); ?>
                            <?php $PSC_Common_Function->session_cart_destroy(); ?>
                        </address>
                    </div><!-- /.col-2 -->
                </div>


                <?php
            }
        } else {
            wp_redirect(get_permalink($PSC_Common_Function->psc_shop_page()));
        }
        ?>
    </div>
</div>