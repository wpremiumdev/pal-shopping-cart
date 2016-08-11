<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$PSC_Common_Function = new PSC_Common_Function();
$PSC_ERROR_DISPLAY_NOTICE_COUPONS = ($PSC_Common_Function->session_get('coupon_cart_discount_msg')) ? $PSC_Common_Function->session_get('coupon_cart_discount_msg') : '';
$PSC_UPDATE_CART_DISPLAY_NOTICE = ($PSC_Common_Function->session_get('update_cart_message')) ? $PSC_Common_Function->session_get('update_cart_message') : '';

$PSC_Common_Function->session_remove('coupon_cart_discount_msg');
$PSC_Common_Function->session_remove('update_cart_message');

$PSC_ERROR_DISPLAY_MSG = '';
if (isset($PSC_ERROR_DISPLAY_NOTICE_COUPONS) && !empty($PSC_ERROR_DISPLAY_NOTICE_COUPONS)) {

    if ($PSC_ERROR_DISPLAY_NOTICE_COUPONS == 'success') {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-success">Coupon code applied successfully.</div>';
    } else {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-error">' . $PSC_ERROR_DISPLAY_NOTICE_COUPONS . '</div>';
    }
}

if (isset($PSC_UPDATE_CART_DISPLAY_NOTICE) && !empty($PSC_UPDATE_CART_DISPLAY_NOTICE)) {

    if ($PSC_UPDATE_CART_DISPLAY_NOTICE == 'success') {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-success">Update cart successfully.</div>';
    } else {
        $PSC_ERROR_DISPLAY_MSG .='<div class="psc-alert-box psc-error">Can\'t Update cart.</div>';
    }
}

echo $PSC_ERROR_DISPLAY_MSG;
?>