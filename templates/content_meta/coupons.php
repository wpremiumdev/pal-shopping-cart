<?php
if (!defined('ABSPATH')) {
    exit;
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();

$currency_code = $PSC_Common_Function->get_psc_currency();
$currency_symbol = $PSC_Common_Function->get_psc_currency_symbol($currency_code);
$get_current_coupons_details = $PSC_Common_Function->get_post_meta_all($post->ID);
$psc_coupon_amount = isset($get_current_coupons_details['psc_coupon_amount']) ? $get_current_coupons_details['psc_coupon_amount'] : 0;
$psc_coupon_expiry_date = isset($get_current_coupons_details['psc_coupon_expiry_date']) ? $get_current_coupons_details['psc_coupon_expiry_date'] : "";
?>
<div class="wrap">    
    <div class="wrap" id="psc_product_coupon_div">
        <ul>
            <li tab="tab1" class="first current"><?php _e('General', 'pal-shopping-crt') ?></li>                                    
        </ul>
        <div class="tab-content" style="display: block;">
            <table class="widefat" cellspacing="0" style="clear: inherit; border:none">                       
                <tbody>
                    <tr class="">   
                        <th><?php echo esc_html('Discount type'); ?></th>
                        <td>
                            <select name="psc_coupon_discount_type">
                                <option value="fixed_cart"> <?php _e('Cart Discount', 'pal-shopping-crt') ?></option>                                                                
                            </select>
                        </td>
                    </tr>
                    <tr>   
                        <th><?php echo _e('Coupon amount', 'pal-shopping-crt').' ('.$currency_symbol.') '; ?></th>
                        <td>
                            <input type="text" name="psc_coupon_amount" value="<?php echo esc_attr($psc_coupon_amount); ?>" id="psc_coupon_amount" placeholder="0">
                        </td>
                    </tr>                    
                    <tr>   
                        <th><?php _e('Coupon expiry date', 'pal-shopping-crt') ?></th>
                        <td>
                            <input type="text" class="psc_coupon_expiry_date" name="psc_coupon_expiry_date" value="<?php echo esc_attr($psc_coupon_expiry_date); ?>" id="psc_coupon_expiry_date" placeholder="YYYY-MM-DD">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>                
    </div>    
</div>