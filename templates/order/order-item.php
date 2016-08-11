<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$order_item_output = "";
$PSC_Common_Function = new PSC_Common_Function();
$get_order_item_details = $PSC_Common_Function->get_order_item_details($post->ID);
$final_total = $get_order_item_details['final_total'];
$currency_symbol = $get_order_item_details['currency_symbol'];
$coupon_cart_discount_array = $get_order_item_details['order_cart_discount'];
//$order_cart_discount_coupon_code = $get_order_item_details['order_cart_discount_coupon_code'];
$order_cart_discount = 0;
$is_psc_tax = (get_post_meta($post->ID, '_order_responce_total_tax', TRUE))?get_post_meta($post->ID, '_order_responce_total_tax', TRUE):0;
$is_psc_ship = (get_post_meta($post->ID, '_order_responce_total_shipping', TRUE))?get_post_meta($post->ID, '_order_responce_total_shipping', TRUE):0;
unset($get_order_item_details['final_total']);
unset($get_order_item_details['currency_symbol']);
unset($get_order_item_details['order_cart_discount']);
unset($get_order_item_details['order_cart_discount_coupon_code']);
?>
<div class="psc_order_items_wrapper psc-order-items-editable">
    <div class='wrap' >
        <table class="widefat" cellspacing="0" id="psc_order_items">
            <thead>
                <tr>
                    <th class="order_item"><?php echo __('Item', 'pal-shopping-cart'); ?></th>
                    <th class="order_cost"><?php echo __('Cost', 'pal-shopping-cart'); ?></th>
                    <th class="order_qty"><?php echo __('Qty', 'pal-shopping-cart'); ?></th>
                    <th class="order_total"><?php echo __('Total', 'pal-shopping-cart'); ?></th>
                </tr>
            </thead>
            <tbody>                    
                <?php
                foreach ($get_order_item_details as $key => $value) {
                    $order_item_output .='<tr>';
                    $order_item_output .='<td class="thumb"><img src="' . esc_url($value['url']) . '" height="25" width="25"><br>' . esc_html($value['name']) . '</td>';
                    $order_item_output .='<td>' . esc_html($currency_symbol) . '' . number_format(esc_html($value['price']), 2) . '</td>';
                    $order_item_output .='<td>' . esc_html($value['qty']) . '</td>';
                    $order_item_output .='<td>' . esc_html($currency_symbol) . '' . number_format(esc_html($value['subtotal']), 2) . '</td>';
                    $order_item_output .='</tr>';
                }
                echo $order_item_output;
                ?>                   
            </tbody>            
        </table>
        <table class="widefat" cellspacing="0" id="psc_order_total_detail">
            <tfoot class="">
                <tr class="psc_subtotal">
                    <td class="lable"><?php echo __('<strong>Subtotal</strong>', 'pal-shopping-cart') . ': '; ?></td>
                    <td class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($final_total), 2); ?></td>                    
                </tr>
               
                
                 <?php
                if (is_array($coupon_cart_discount_array) && count($coupon_cart_discount_array) > 0) {
                    foreach ($coupon_cart_discount_array as $key => $value) {
                        $order_cart_discount = $order_cart_discount + $value['psc_coupon_amount'];
                        $total_amount_pay = '-' . esc_html($currency_symbol) . '' . number_format(esc_html(str_replace('-', '', $value['psc_coupon_amount'])), 2);
                        if ($value['psc_coupon_amount'] == 0) {
                            $total_amount_pay = "";
                        }
                        ?>
                <tr class="psc_discount">
                            <th class="lable"><?php _e('Discount', 'pal-shopping-crt') ?>: <?php echo esc_html($value['coupon_code']); ?></th>
                            <td class=""><?php echo $total_amount_pay; ?></td>
                            </td>
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
                <tr class="psc_order_total">
                    <td class="lable"><?php echo __('<strong>Order Total</strong>', 'pal-shopping-cart') . ': '; ?></td>
                    <td class="amount"><?php echo esc_html($currency_symbol) . '' . number_format(esc_html($final_total - $order_cart_discount), 2); ?></td>                    
                </tr>                            
            </tfoot>
        </table>
    </div>
</div>