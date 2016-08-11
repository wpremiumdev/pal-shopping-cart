<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$psc_card_handler_obj = new PSC_Cart_Handler();
$PSC_Common_Function = new PSC_Common_Function();
$cart_item_array = $psc_card_handler_obj->contents();
$currency_code = $PSC_Common_Function->get_psc_currency();
$currency_symbol = Paypal_Shopping_Cart_General_Setting::get_paypal_shopping_cart_currency_symbol($currency_code);
//$coupon_cart_discount = $PSC_Common_Function->get_cart_total_discount();
$coupon_code = $PSC_Common_Function->get_cart_total_coupon_code();
$total_checkout_price = 0;
$coupon_cart_discount_array = array();
$coupon_cart_discount_array = $PSC_Common_Function->remove_cart_coupon_in_array();
$is_remove_coupon_enable = isset($_GET['remove_coupon'])?$_GET['remove_coupon']:'';
$coupon_cart_discount_array = $PSC_Common_Function->remove_cart_coupon_in_array($is_remove_coupon_enable);
$coupon_cart_discount = $PSC_Common_Function->psc_get_cart_total_discount();
$is_psc_tax = 0;
$is_psc_ship = 0;
if (is_array($cart_item_array)) {
    ?>
    <table class="psc-checkout-review-order-table">
        <thead>
            <tr>
                <th class="psc-product-name"><?php _e('Product', 'pal-shopping-crt') ?></th>
                <th class="psc-product-total"><?php _e('Total', 'pal-shopping-crt') ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($cart_item_array as $key => $value) { ?>                
                <tr class="psc_cart_item">
                    <td class="psc_product-name">
                        <strong class="psc_product-quantity"><?php echo esc_html($value['name']) . ' × ' . esc_html($value['qty']); ?></strong>	
                    </td>
                    <td class="psc_product-total">
                        <span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($value['subtotal']), 2); ?></span>
                    </td>
                </tr>
                <?php
                if (isset($value['tax']) && !empty($value['tax'])) {
                    $qty_tax = $value['qty'] * $value['tax'];
                    $is_psc_tax = $is_psc_tax + $qty_tax;
                }
                if (isset($value['shipping']) && !empty($value['shipping'])) {
                    $qty_ship = $value['qty'] * $value['shipping'];
                    $is_psc_ship = $is_psc_ship + $qty_ship;
                }
                $total_checkout_price = $total_checkout_price + $value['subtotal'];
            }
            ?>

        </tbody>
        <tfoot>
            <tr class="cart-subtotal">
                <th><?php _e('Subtotal', 'pal-shopping-crt') ?></th>
                <td>
                    <span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($total_checkout_price), 2); ?></span>
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
                                <td><strong><span class="amount"><?php echo $total_amount_pay; ?></span></strong><span class="psc_remove_discount" data-coupon-id="<?php echo $key; ?>"><?php echo apply_filters('psc_remove_coupon', sprintf('<a href="%s" class="psc-remove-coupon">%s</a>', add_query_arg('remove_coupon', $value['coupon_code'], get_permalink($post->ID)),esc_attr('[Remove]')), $post); ?></span> </td>
                    </td>
                </tr>
                            <?php
                        }
                    }
            if (isset($is_psc_tax) && !empty($is_psc_tax)) {
                ?><tr class="psc-order-tax"><th><?php echo _e("TAX", "pal-shopping-crt"); ?></th><td><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_tax), 2); ?></span></td></tr><?php
            }

            if (isset($is_psc_ship) && !empty($is_psc_ship)) {
                ?><tr class="psc-order-shipping"><th><?php echo _e("Shipping", "pal-shopping-crt"); ?></th><td><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_ship), 2); ?></span></td><?php
                            }
                    ?>
            <tr class="order-total">
                <th><?php _e('Total', 'pal-shopping-crt') ?></th>

                                    <?php
                if (isset($psc_card_handler_obj->_cart_contents['cart_total']) && $psc_card_handler_obj->_cart_contents['cart_total'] > 0) {
                                           $total_amount = $psc_card_handler_obj->_cart_contents['cart_total'] - $coupon_cart_discount;                                                                                       
                                           $total_amount = ($total_amount > 0)?$total_amount:0;
                    $total_amount = $total_amount + $is_psc_ship + $is_psc_tax;
                                        ?>
                                            <td><strong><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($total_amount), 2); ?></span></strong> </td>
            <?php }
            ?>            
            </tr>
        </tfoot>
    </table>
    <div id="psc-payment" class="psc-checkout-payment">        
    <?php do_action('psc_get_all_enable_payment_methods'); ?>
    </div>

<?php }