<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post, $psc_loop;

$PSC_Common_Function = new PSC_Common_Function();

$display_outofstock_product = get_option('psc_shop_display_outofstock_product') ? get_option('psc_shop_display_outofstock_product') : 'yes';

$is_product_stock = true;

if ($display_outofstock_product == 'no') {
    $is_product_stock = $PSC_Common_Function->get_stock_status_by_post_id($post->ID);
}

$is_product_price_empty = $PSC_Common_Function->psc_product_price_empty($post->ID);

if ($is_product_stock) {

    if ($is_product_price_empty) {

        $psc_column = (get_option('psc_column_product_settings')) ? get_option('psc_column_product_settings') : 4;
        if (empty($psc_loop['loop'])) {
            $psc_loop['loop'] = 0;
        }
        if (empty($psc_loop['columns'])) {
            $psc_loop['columns'] = apply_filters('loop_shop_columns', $psc_column);
        }

        $psc_loop['loop']++;

        $classes = array();
        if (0 == ( $psc_loop['loop'] - 1 ) % $psc_loop['columns'] || 1 == $psc_loop['columns']) {
            $classes[] = 'first';
        }
        if (0 == $psc_loop['loop'] % $psc_loop['columns']) {
            $classes[] = 'last';
        }
        ?>

        <li <?php psc_product_cat_class($classes); ?>>

            <?php do_action('psc_before_shop_loop_item'); ?>

            <a href="<?php the_permalink(); ?>">

                <?php
                do_action('psc_before_shop_loop_item_title');

                do_action('psc_shop_loop_item_title');

                do_action('psc_after_shop_loop_item_title');
                ?>
            </a>
            <?php
            do_action('psc_after_shop_loop_item');
            ?>
        </li>
        <?php
    }
}
?>