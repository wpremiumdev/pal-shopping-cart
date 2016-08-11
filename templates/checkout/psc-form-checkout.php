<?php
if (!defined('ABSPATH')) {
    exit;
}

global $post, $empty_filed;
$PSC_Common_Function = new PSC_Common_Function();
$PSC_Common_Function->is_empty_filed_found();
$psc_shop_page_id = $PSC_Common_Function->psc_shop_page();
$cart_item_array = $PSC_Common_Function->session_cart_contents();
$psc_pec_express_checkout_message = (get_option('psc_pec_express_checkout_message')) ? get_option('psc_pec_express_checkout_message') : '';
?>
<?php
if (is_array($cart_item_array) && count($cart_item_array) > 0) {
    ?>
    <div class="">   
        <div class="psc_display_notice"><?php do_action('psc_display_notice'); ?></div> 


        <?php
        $is_result = $PSC_Common_Function->enable_paypal_express_checkout_button();
        if ($PSC_Common_Function->get_cart_total_is_empty() && $is_result) {
            ?>
            <div class="psc-enable_express-checkout">    
                <?php
                do_action('enable_checkout_button', 'checkout', '');
                ?>
                <p class="checkoutStatus"><?php echo apply_filters('psc_checkout_button_faster_with_paypal', __($psc_pec_express_checkout_message, 'pal-shopping-cart')); ?></p> </div>     
            <?php
        }
        ?>    
        <div class="paypal-shopping-carts-checkout">         
            <form action="" name="psc_checkout" method="post" id="psc_checkout_process_now" class="checkout psc-form-style-9" id="psc-form-style-9" action="" enctype="multipart/form-data">
                <div class="psc-col2-set" id="psc_customer_billing_details">
                    <div id="psc_billing_div" class="psc-col-1" style="">
                        <?php do_action('psc_checkout_form_billing'); ?>
                    </div>            
                </div>
                <?php do_action('psc_checkout_after_customer_details'); ?>
                <h3 id="psc_order_heading"><?php _e('Your order', 'pal-shopping-cart'); ?></h3>
                <div id="psc_order_review" class="psc-checkout-review-order">
                    <?php do_action('psc_checkout_before_order_review'); ?>
                </div>
            </form>
        </div>
    </div>
<?php } else { ?>

    <div class="psc-return-shop-page">
        <p><?php echo esc_html('Your cart is currently empty.'); ?></p>
        <a class="psc-button" href="<?php echo esc_url(get_permalink($psc_shop_page_id)); ?>">Return to Shop Page</a>
    </div>      
<?php } ?>