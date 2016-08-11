<?php
if (!defined('ABSPATH')) {
    exit;
}

global $post;
$psc_card_handler_obj = new PSC_Cart_Handler();
$PSC_Common_Function = new PSC_Common_Function();
$country_obj = new PSC_Countries();
$express_methods_obj = new Paypal_Shopping_Cart_Express_Checkout();
$total_checkout_price = 0;
$shiptocountry = '';
$pscorder = $PSC_Common_Function->session_cart_contents();
$country_array = $country_obj->Countries();
$currency_code = $PSC_Common_Function->get_psc_currency();
$currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
$coupon_code = $PSC_Common_Function->get_cart_total_coupon_code();
$coupon_cart_discount_array = array();
$coupon_cart_discount_array = $PSC_Common_Function->remove_cart_coupon_in_array();
$is_remove_coupon_enable = isset($_GET['remove_coupon']) ? $_GET['remove_coupon'] : '';
$coupon_cart_discount_array = $PSC_Common_Function->remove_cart_coupon_in_array($is_remove_coupon_enable);
$coupon_cart_discount = $PSC_Common_Function->psc_get_cart_total_discount();
$get_shiptoname = $PSC_Common_Function->session_get('shiptoname');
if (isset($get_shiptoname) && empty($get_shiptoname)) {

    if (isset($_GET['token']) && !empty($_GET['token'])) {
        $paypal_express_checkout = new Paypal_Shopping_Cart_Express_Checkout();
        $paypal_express_checkout->paypal_express_checkout($_GET);
    }
}
$is_psc_tax = 0;
        $is_psc_ship = 0;
?>
<div >
    <div class="pscpaypalexpress_order_review">
        <form class="paypal_express_checkout" method="POST" action="<?php echo add_query_arg(array('psc-api' => 'Paypal_Shopping_Cart_Express_Checkout', 'psc_action' => 'place-order'), home_url()); ?>">
            <table class="psc-checkout-review-order-table">
                <thead>
                    <tr>
                        <th class="psc-product-name"><?php echo __('Product', 'pal-shopping-cart'); ?></th>
                        <th class="psc-product-total"><?php echo __('Total', 'pal-shopping-cart'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pscorder as $key => $value) { 
                        $qty_tax = $value['qty'] * $value['tax'];
                            $is_psc_tax = $is_psc_tax + $qty_tax;
                            
                            $qty_ship = $value['qty'] * $value['shipping'];
                            $is_psc_ship = $is_psc_ship + $qty_ship;
                        
                        ?>                
                        <tr class="psc_cart_item">
                            <td class="psc_product-name">
                                <strong class="psc_product-quantity"><?php echo esc_html($value['name'] . ' × ' . $value['qty']); ?></strong>	
                            </td>
                            <td class="psc_product-total">
                                <span class="amount"><?php echo esc_html($currency_symbol . '' . number_format($value['subtotal'], 2)); ?></span>
                            </td>
                        </tr>
                        <?php
                        $total_checkout_price = $total_checkout_price + $value['subtotal'];
                    }
                    ?>

                </tbody>
                <tfoot>
                    <tr class="cart-subtotal">
                        <th><?php echo __('Subtotal', 'pal-shopping-cart'); ?></th>
                        <td>
                            <span class="amount"><?php echo esc_html($currency_symbol . '' . number_format($total_checkout_price, 2)); ?></span>
                        </td>
                    </tr>
                    <?php
                    if (is_array($coupon_cart_discount_array) && count($coupon_cart_discount_array) > 0) {
                        foreach ($coupon_cart_discount_array as $key => $value) {
                            $total_amount_pay = '-' . esc_html($currency_symbol) . '' . number_format(esc_html(str_replace('-', '', $value['psc_coupon_amount'])), 2);
                            if ($value['psc_coupon_amount'] == 0) {
                                $total_amount_pay = "";
                            }
                            ?>
                            <tr class="cart-coupons">  
                                <th><?php _e('Coupons', 'pal-shopping-crt') ?>: <?php echo esc_html($value['coupon_code']); ?></th>
                                <td><strong><span class="amount"><?php echo $total_amount_pay; ?></span></strong><span class="psc_remove_discount" data-coupon-id="<?php echo $key; ?>"><?php echo apply_filters('psc_remove_coupon', sprintf('<a href="%s" class="psc-remove-coupon">%s</a>', add_query_arg('remove_coupon', $value['coupon_code'], get_permalink($post->ID)), esc_attr('[Remove]')), $post); ?></span> </td>
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
                    <tr class="order-total">
                        <th><?php echo __('Total', 'pal-shopping-cart'); ?></th>
                        <?php
                        if (isset($psc_card_handler_obj->_cart_contents['cart_total']) && $psc_card_handler_obj->_cart_contents['cart_total'] > 0) {
                            $total_amount = $psc_card_handler_obj->_cart_contents['cart_total'] - $coupon_cart_discount;
                            $total_amount = ($total_amount > 0) ? $total_amount : 0;
                            $total_amount = $total_amount + $is_psc_ship + $is_psc_tax;
                            ?>
                            <td><strong><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($total_amount), 2); ?></span></strong> </td>
                        <?php }
                        ?>
                    </tr>
                </tfoot>
            </table>
            <?php
            if (isset($_GET['psc_action']) && 'pscrevieworder' == $_GET['psc_action']) {
                $express_methods_obj->paypal_express_checkout($_POST);
            }
            foreach ($country_array as $key => $value) {
                if ($key == $PSC_Common_Function->session_get('shiptocountrycode')) {
                    $shiptocountry = $value;
                }
            }
            if ($total_checkout_price != 0) {
                if ($PSC_Common_Function->session_get('shiptoname')) {
                    ?>
                    <!--                    <div class="title">
                                            <h2><?php //echo __('Customer details', 'pal-shopping-cart');  ?></h2>
                                        </div>-->
                    <div class="col2-set addresses">
                        <div class="col-1">
                            <div class="title">
                                <h3><?php echo __('Shipping Address', 'pal-shopping-cart'); ?></h3>
                            </div>
                            <div class="address">
                                <p>
                                    <?php
                                    $is_set_session = $PSC_Common_Function->session_get('shiptoname');
                                    if (isset($is_set_session) && !empty($is_set_session)) {
                                        echo $PSC_Common_Function->get_customer_address();
                                    }
                                    ?> 
                                </p>
                            </div>
                        </div>
                        <div class="col-2"></div>
                    </div>

                    <div class="clear"></div>
                    <p class="pac_button_action_click">             
                        <a class="button paypal_express_cancel pac_button_action_click_submit class_to_defualt_cursor" href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_cancel_page_url())) ?>"><?php echo __('Cancel Order', 'pal-shopping-cart'); ?></a> 
                        <input type="submit"  class="button pac_button_action_click_submit class_to_defualt_cursor" value="<?php echo __('Place Order', 'pal-shopping-cart'); ?>">
                    </p>
                <?php
                }
            } else {
                ?>
                <div class="title">
                    <h2 style="color:#b81c23;"><?php echo __('Your Review Order is empty!.', 'pal-shopping-cart'); ?></h2>                    
                    <a  class="psc-button" href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_shop_page())); ?>"><?php echo __('Return to Shop Page', 'pal-shopping-cart'); ?></a>
                </div>
<?php } ?>
            </p>
        </form>
    </div>
</div>