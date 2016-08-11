<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php

$PSC_Common_Function = new PSC_Common_Function();
$PSC_STOCK_DISPLAY_NOTICE = ($PSC_Common_Function->session_get('add_to_cart_stock_is_empty')) ? $PSC_Common_Function->session_get('add_to_cart_stock_is_empty') : '';
$PSC_STOCK_DISPLAY_NOTICE_QTY = ($PSC_Common_Function->session_get('add_to_cart_stock_is_qty')) ? $PSC_Common_Function->session_get('add_to_cart_stock_is_qty') : '';


$PSC_STOCK_DISPLAY_NOTICE_ITEM = ($PSC_Common_Function->session_get('add_to_cart_stock_is_big_item')) ? $PSC_Common_Function->session_get('add_to_cart_stock_is_big_item') : '';
$PSC_STOCK_DISPLAY_NOTICE_QTY_ITEM_ADD = ($PSC_Common_Function->session_get('add_to_cart_stock_is_item_qty_add')) ? $PSC_Common_Function->session_get('add_to_cart_stock_is_item_qty_add') : '';



$PSC_Common_Function->session_remove('add_to_cart_stock_is_empty');
$PSC_Common_Function->session_remove('add_to_cart_stock_is_qty');

$PSC_Common_Function->session_remove('add_to_cart_stock_is_big_item');
$PSC_Common_Function->session_remove('add_to_cart_stock_is_item_qty_add');

$PSC_ERROR_DISPLAY_MSG = '';

if (isset($PSC_STOCK_DISPLAY_NOTICE) && !empty($PSC_STOCK_DISPLAY_NOTICE)) {

    if ($PSC_STOCK_DISPLAY_NOTICE == 'empty') {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-error">You can not add that amount to the cart — we have ' . $PSC_STOCK_DISPLAY_NOTICE_QTY . ' in stock and you already have ' . $PSC_STOCK_DISPLAY_NOTICE_QTY . ' in your cart.<span class="view_full_cart"><a href="' . get_permalink($PSC_Common_Function->psc_cart_page()) . '">View Cart</a></span></div>';
    }
}

if (isset($PSC_STOCK_DISPLAY_NOTICE_ITEM) && !empty($PSC_STOCK_DISPLAY_NOTICE_ITEM)) {

    if ($PSC_STOCK_DISPLAY_NOTICE_ITEM == 'item') {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-error">Product Stock is ' . $PSC_STOCK_DISPLAY_NOTICE_QTY_ITEM_ADD . ' please enter QTY less then ' . $PSC_STOCK_DISPLAY_NOTICE_QTY_ITEM_ADD . '.</div>';
    }
}

echo $PSC_ERROR_DISPLAY_MSG;
?>