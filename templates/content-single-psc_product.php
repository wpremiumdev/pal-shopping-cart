<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="psc_display_notice">
    <?php 
        do_action('psc_display_notice');  
        do_action('psc_display_notice_empty_stock');
    ?>
</div>
<!--<div class="psc_display_notice"><?php //do_action('psc_display_notice_empty_stock'); ?></div>-->
<div id="product-<?php the_ID(); ?>" class="psc-single-image">
    <?php
    do_action('psc_before_single_product_summary');
    ?>
    <div class="psc-summary psc-entry-summary">
        <?php
        do_action('psc_single_product_summary');
        ?>
    </div>
    <?php
    do_action('psc_after_single_product_summary');
    ?>
    <meta itemprop="url" content="<?php the_permalink(); ?>" />
</div>