<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;

$PSC_Common_Function = new PSC_Common_Function();
$psc_short_dec = $PSC_Common_Function->psc_short_dec($post);

if (!$psc_short_dec) {
    return;
}
?>
<div itemprop="description">
    <p><?php echo do_shortcode($psc_short_dec); ?></p>
</div>