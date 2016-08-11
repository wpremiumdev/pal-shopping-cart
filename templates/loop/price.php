<?php
/**
 * Loop Price
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$PSC_Common_Function = new PSC_Common_Function();
$price_html = $PSC_Common_Function->psc_get_price_html($post)
?>
<?php if ($price_html) : ?>
    <span class="price"><?php echo $price_html; ?></span>
<?php endif; ?>