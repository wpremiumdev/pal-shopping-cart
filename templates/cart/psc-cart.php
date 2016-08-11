<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$PSC_Common_Function = new PSC_Common_Function();
$psc_shop_page_id = $PSC_Common_Function->psc_shop_page();
$currency_symbol = $PSC_Common_Function->get_psc_currency_symbol_only();
$cart_item_array = '';
$psc_card_handler_obj = new PSC_Cart_Handler();
//customer session remove
$is_customer_session = $PSC_Common_Function->session_get('TOKEN');
if (isset($is_customer_session) && strlen($is_customer_session) > 0) {
    $PSC_Common_Function->customer_session_empty();
}
$coupon_cart_discount_array = array();

$PSC_Common_Function->update_cart_coupon_in_array();
$is_remove_coupon_enable = isset($_GET['remove_coupon']) ? $_GET['remove_coupon'] : '';
$coupon_cart_discount_array = $PSC_Common_Function->remove_cart_coupon_in_array($is_remove_coupon_enable);
$coupon_cart_discount = $PSC_Common_Function->psc_get_cart_total_discount();


$coupon_code = $PSC_Common_Function->get_cart_total_coupon_code();
$cart_item_array = $psc_card_handler_obj->contents();
$psc_coupons_general_settings = (get_option('psc_coupons_general_settings')) ? get_option('psc_coupons_general_settings') : 'yes';
$psc_coupons_general_settings = ($psc_coupons_general_settings) == 'yes' ? true : false;

$is_psc_tax = 0;
$is_psc_ship = 0;

if (is_array($cart_item_array) && count($cart_item_array) > 0) {
    ?>
    <div class="">
        <div class="psc_display_notice"><?php do_action('psc_display_notice'); ?></div> 
        <div class="psc_shop_table_div">    
            <div class="psc_display_notice">
                <?php do_action('psc_display_notice_coupons'); ?>
            </div>
            <form action="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_cart_page())); ?>" method="post">
                <table class="psc_shop_table cart" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="psc-product-remove"></th>
                            <th class="psc-product-thumbnail"></th>
                            <th class="psc-product-name"><?php _e('Product', 'pal-shopping-cart'); ?></th>
                            <th class="psc-product-price"><?php _e('Price', 'pal-shopping-cart'); ?></th>
                            <th class="psc-product-quantity"><?php _e('Quantity', 'pal-shopping-cart'); ?></th>
                            <th class="psc-product-subtotal"><?php _e('Total', 'pal-shopping-cart'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        $total_price = 0;
                        foreach ($cart_item_array as $key => $value) {
                            $expload_data = explode(':', $value['name']);
                            $expload_product_id = explode('_', $value['id']);
                            $expload_data0 = "";
                            $expload_data1 = "";
                            if (is_array($expload_data) && count($expload_data) > 0) {
                                $expload_data0 = isset($expload_data[0]) ? $expload_data[0] : '';
                                $expload_data1 = isset($expload_data[1]) ? $expload_data[1] : '';
                            }

                            $is_stock_is = $PSC_Common_Function->get_update_stock_by_post_id($expload_product_id[0], $expload_data1);
                            $product_stock = "";
                            if (isset($is_stock_is) && $is_stock_is > 0) {
                                $product_stock = $is_stock_is;
                            } else if (isset($is_stock_is) && $is_stock_is == 'instock') {
                                $product_stock = "";
                            }
                            ?>

                            <tr class="psc-product-tr-<?php echo esc_html($count); ?>">
                                <td class="psc-product-remove"><?php echo "<span class='psc-product-remove-icon' data-product-id='" . esc_attr($expload_product_id[0]) . "' data-row-id='" . esc_attr($value['rowid']) . "'><img src='" . esc_url(apply_filters('psc_placeholder_img_src', plugins_url('../public/images/remove_item.png', dirname(__FILE__)))) . "'></span>" ?></td>
                                <td class="psc-product-thumbnail">
                                    <?php
                                    $thumbnail = $PSC_Common_Function->cart_get_image($value['id']);

                                    if ($thumbnail) {
                                        echo $thumbnail;
                                    } else {
                                        printf('<a href="%s">%s</a>', esc_url($PSC_Common_Function->get_permalink($value['id'])), $thumbnail);
                                    }
                                    ?>
                                </td>
                                <td class="psc-product-name"><a href="<?php the_permalink($expload_product_id[0]); ?>"><?php echo esc_html($expload_data0) . '<br />'; ?></a><strong data-variation-name="<?php echo esc_html($expload_data1); ?>"><?php echo esc_html($expload_data1); ?></strong></td>
                                <td class="psc-product-price"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($value['price']), 2); ?></td>
                                <td class="psc-product-quantity"><input type="number" id="psc_update_item_qty" name="psc_update_item_qty" value="<?php echo esc_html($value['qty']); ?>" min="1" max="<?php echo esc_html($product_stock); ?>"></td>
                                <td class="psc-product-subtotal"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($value['subtotal']), 2); ?></td>
                            </tr> 
                            <?php
                            
                            if( isset($value['tax']) && !empty($value['tax']) ){
                                
                                $qty_tax = $value['qty'] * $value['tax'];
                                $is_psc_tax = $is_psc_tax + $qty_tax;
                            }
                            if( isset($value['shipping']) && !empty($value['shipping']) ){
                                $qty_ship = $value['qty'] * $value['shipping'];
                                $is_psc_ship = $is_psc_ship + $qty_ship;
                            }
                            $total_price = $total_price + $value['subtotal'];
                            $count++;
                        }
                        ?>
                        <tr>
                            <td colspan="6"> 
                                <?php if ($psc_coupons_general_settings) { ?>                            
                                    <input type="text" name="psc_applay_coupons_text" id="psc_applay_coupons_text" value="">                            
                                    <span id="psc_applay_coupons" class="psc-button"><?php _e('Apply Coupon'); ?></span>
                                <?php } ?>
                                <span id="psc_update_cart" class="psc-button"><?php _e('Update Cart'); ?></span>
                            </td>
                        </tr>    


                    </tbody>
                </table>
                <div class="psc-totals-checkout">   
                    <div class="psc-total-carts">
                        <h2><?php _e('Cart Totals', 'pal-shopping-crt') ?></h2>
                        <table cellspacing="0" class="psc-chackout-table">
                            <tbody><tr class="psc-cart-subtotal">
                                    <th><?php _e('Subtotal', 'pal-shopping-crt') ?></th>
                                    <td><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($total_price), 2); ?></span></td>
                                </tr>


                                <?php
                                if (is_array($coupon_cart_discount_array) && count($coupon_cart_discount_array) > 0) {
                                    foreach ($coupon_cart_discount_array as $key => $value) {

                                        $total_amount_pay = '-' . esc_html($currency_symbol) . '' . number_format(esc_html(str_replace('-', '', $value['psc_coupon_amount'])), 2);
                                        if ($value['psc_coupon_amount'] == 0) {
                                            $total_amount_pay = "";
                                        }
                                        ?>
                                    <tr class="psc-cart-coupon">
                                            <th><?php _e('Coupons', 'pal-shopping-crt') ?>: <?php echo esc_html($value['coupon_code']); ?></th>
                                            <td><strong><span class="amount"><?php echo $total_amount_pay; ?></span></strong><span class="psc_remove_discount" data-coupon-id="<?php echo $key; ?>"><?php echo apply_filters('psc_remove_coupon', sprintf('<a href="%s" class="psc-remove-coupon">%s</a>', add_query_arg('remove_coupon', $value['coupon_code'], get_permalink($post->ID)), esc_attr('[Remove]')), $post); ?></span> </td>
                                    </tr>
                                        <?php
                                    }
                                }
                                
                                if( isset($is_psc_tax) && !empty($is_psc_tax) ){                                    
                                    ?><tr class="psc-order-tax"><th><?php echo _e("TAX", "pal-shopping-crt");?></th><td><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_tax), 2); ?></span></td></tr><?php
                                }
                                
                                if( isset($is_psc_ship) && !empty($is_psc_ship) ){
                                    ?><tr class="psc-order-shipping"><th><?php echo _e("Shipping", "pal-shopping-crt");?></th><td><span class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($is_psc_ship), 2); ?></span></td><?php
                                }
                                ?>
                                <tr class="psc-order-total">
                                    <th><?php _e('Total', 'pal-shopping-crt') ?></th>

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
                            </tbody>
                        </table>
                        <div class="psc-proceed-to-checkout">
                            <?php
                            if( $PSC_Common_Function->get_cart_total_is_empty() ){
                            $psc_pec_enabled = (get_option('psc_pec_enabled')) ? get_option('psc_pec_enabled') : 'no';
                            $enable_checkout_button = (get_option('psc_pec_cart_page_enabled_button')) ? get_option('psc_pec_cart_page_enabled_button') : 'yes';
                            $psc_pec_standaed_checkout_button = (get_option('psc_pec_standaed_checkout_button')) ? get_option('psc_pec_standaed_checkout_button') : 'no';
                            $is_result = $PSC_Common_Function->enable_paypal_express_checkout_button();
                            if ($is_result) {
                                do_action('enable_checkout_button', 'cart', '');
                            }

                            if ($psc_pec_standaed_checkout_button == 'yes') {
                                if ($psc_pec_enabled == 'no' || $enable_checkout_button == 'no') {
                                    ?>
                                    <a id="" href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_checkout_page())); ?>" class="psc-button psc_proceed_to_checkout"><?php echo (get_option('psc_cart_button_general_settings')) ? get_option('psc_cart_button_general_settings') : 'Proceed to Checkout'; ?></a>
                                    <?php
                                }
                            } else if ($psc_pec_standaed_checkout_button == 'no') {
                                ?>

                                <a id="" href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_checkout_page())); ?>" class="psc-button psc_proceed_to_checkout"><?php echo (get_option('psc_cart_button_general_settings')) ? get_option('psc_cart_button_general_settings') : 'Proceed to Checkout'; ?></a>

                            <?php }
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </form>
        </div>   
    </div>

<?php } else { ?>
    <div class="psc-return-shop-page">
        <p><?php echo esc_html('Your cart is currently empty.'); ?></p>
        <a  class="psc-button" href="<?php echo esc_url(get_permalink($psc_shop_page_id)); ?>">Return to Shop Page</a>
    </div> 
<?php } ?>
