<?php
/**
 * Single Product Thumbnails
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();

$attachment_ids = 0;
if ($attachment_ids) {
    $loop = 0;
    $columns = apply_filters('psc_product_thumbnails_columns', 3);
    ?>
    <div class="thumbnails <?php echo esc_html('columns-' . $columns); ?>"><?php
        foreach ($attachment_ids as $attachment_id) {

            $classes = array('zoom');

            if ($loop == 0 || $loop % $columns == 0)
                $classes[] = 'first';

            if (( $loop + 1 ) % $columns == 0)
                $classes[] = 'last';

            $image_link = wp_get_attachment_url($attachment_id);

            if (!$image_link)
                continue;

            $image_title = esc_attr(get_the_title($attachment_id));
            $image_caption = esc_attr(get_post_field('post_excerpt', $attachment_id));

            $image = wp_get_attachment_image($attachment_id, apply_filters('single_product_small_thumbnail_size', 'psc_shop_thumbnail'), 0, $attr = array(
                'title' => $image_title,
                'alt' => $image_title
            ));

            $image_class = esc_attr(implode(' ', $classes));

            echo apply_filters('psc_single_product_image_thumbnail_html', sprintf('<a href="%s" class="%s" title="%s" data-rel="prettyPhoto[product-gallery]">%s</a>', $image_link, $image_class, $image_caption, $image), $attachment_id, $post->ID, $image_class);

            $loop++;
        }
        ?></div>
    <?php
}