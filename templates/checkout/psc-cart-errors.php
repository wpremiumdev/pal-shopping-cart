<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $PSC_ERROR_DISPLAY_NOTICE;
$PSC_ERROR_DISPLAY = '';
if (isset($PSC_ERROR_DISPLAY_NOTICE) && is_array($PSC_ERROR_DISPLAY_NOTICE)) {
    foreach ($PSC_ERROR_DISPLAY_NOTICE as $key => $value) {
        $PSC_ERROR_DISPLAY .='<div class="psc-alert-box psc-error"><span>Error: </span>' . $value['L_LONGMESSAGE'] . '</div>';
    }
    $PSC_ERROR_DISPLAY_NOTICE = '';
}
echo $PSC_ERROR_DISPLAY;
?>