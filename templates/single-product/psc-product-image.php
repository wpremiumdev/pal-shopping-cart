<?php
/**
 * Single Product Image
 * 
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();
?>
<div class="images">

    <?php
    if (has_post_thumbnail()) {

        $image_title = esc_attr(get_the_title(get_post_thumbnail_id()));
        $image_caption = get_post(get_post_thumbnail_id())->post_excerpt;
        $image_link = wp_get_attachment_url(get_post_thumbnail_id());
        $image = get_the_post_thumbnail($post->ID, apply_filters('single_product_large_thumbnail_size', 'psc_shop_single'), array(
            'title' => $image_title,
            'alt' => $image_title
        ));
        $gallery = '';
        echo apply_filters('psc_single_product_image_html', sprintf('<a href="%s" itemprop="image" class="psc-main-image zoom" title="%s" data-rel="prettyPhoto' . esc_html($gallery) . '">%s</a>', esc_url($image_link), esc_html($image_caption), $image), $post->ID);
    } else {

        echo apply_filters('psc_single_product_image_html', sprintf('<img src="%s" alt="%s" />', placeholder_img_src(), __('Placeholder', 'pal-shopping-cart')), $post->ID);
    }
    ?>
    <?php do_action('psc_product_thumbnails'); ?>

</div>