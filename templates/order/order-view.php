<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$Billing_address = "";
$Shipping_address = "";
$PSC_Common_Function = new PSC_Common_Function();
$get_order_all_details = $PSC_Common_Function->get_order_all_details($post->ID);
$TRANSACTIONID = isset($get_order_all_details[1][0]['TRANSACTIONID']) ? $get_order_all_details[1][0]['TRANSACTIONID'] : '';
$PAYMENT_METHOD_TITLE = isset($get_order_all_details[0]['_payment_method_title']) ? $get_order_all_details[0]['_payment_method_title'] : '';
?>
<div id="order_view" class="panel">
    <h1>Order #<?php echo esc_html($post->ID); ?> details</h1>
    <p class="pscorder_payment_methods"><?php echo esc_html($PAYMENT_METHOD_TITLE . ' (' . $TRANSACTIONID . ')'); ?></p>
    <div class="pscorder_data_column_container">
        <div class="order_data_column">

            <h4 class="address_header"><?php echo __('Billing Details Address', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php
                $Billing_address .= ($get_order_all_details[0]['_shipping_company']) ? esc_html($get_order_all_details[0]['_shipping_company']) . '<br>' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_full_name']) ? esc_html($get_order_all_details[0]['_shipping_full_name']) . '<br>' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_address_1']) ? esc_html($get_order_all_details[0]['_shipping_address_1']) . '<br>' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_address_2']) ? esc_html($get_order_all_details[0]['_shipping_address_2']) . '<br>' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_city']) ? esc_html($get_order_all_details[0]['_shipping_city']) . esc_html('-') : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_postcode']) ? esc_html($get_order_all_details[0]['_shipping_postcode']) . '<br>' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_state']) ? esc_html($get_order_all_details[0]['_shipping_state']) . ', ' : '';
                $Billing_address .= ($get_order_all_details[0]['_shipping_country']) ? esc_html($get_order_all_details[0]['_shipping_country']) : '';
                echo $Billing_address;
                ?>
            </p>
            <h4 class="address_header"><?php echo __('Email', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php echo ($get_order_all_details[0]['_billing_email']) ? esc_html($get_order_all_details[0]['_billing_email']) : ''; ?>
            </p>
            <h4 class="address_header"><?php echo __('Phone', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php echo ($get_order_all_details[0]['_billing_phone']) ? esc_html($get_order_all_details[0]['_billing_phone']) : ''; ?>
            </p>
        </div>
        <div class="order_data_column">

            <h4 class="address_header"><?php echo __('Shipping Details Address', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php
                $Shipping_address .= ($get_order_all_details[0]['_shipping_company']) ? esc_html($get_order_all_details[0]['_shipping_company']) . '<br>' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_full_name']) ? esc_html($get_order_all_details[0]['_shipping_full_name']) . '<br>' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_address_1']) ? esc_html($get_order_all_details[0]['_shipping_address_1']) . '<br>' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_address_2']) ? esc_html($get_order_all_details[0]['_shipping_address_2']) . '<br>' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_city']) ? esc_html($get_order_all_details[0]['_shipping_city']) . esc_html('-') : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_postcode']) ? esc_html($get_order_all_details[0]['_shipping_postcode']) . '<br>' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_state']) ? esc_html($get_order_all_details[0]['_shipping_state']) . ', ' : '';
                $Shipping_address .= ($get_order_all_details[0]['_shipping_country']) ? esc_html($get_order_all_details[0]['_shipping_country']) : '';
                echo $Shipping_address;
                ?>
            </p>
            <h4 class="address_header"><?php echo __('Email', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php echo ($get_order_all_details[0]['_billing_email']) ? esc_html($get_order_all_details[0]['_billing_email']) : ''; ?>
            </p>
            <h4 class="address_header"><?php echo __('Phone', 'pal-shopping-cart'), ': '; ?></h4>
            <p>
                <?php echo ($get_order_all_details[0]['_billing_phone']) ? esc_html($get_order_all_details[0]['_billing_phone']) : ''; ?>
            </p>
        </div>
    </div>
</div>