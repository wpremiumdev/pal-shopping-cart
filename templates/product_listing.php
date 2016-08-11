<?php

global $psc_query;
wp_reset_query();
$posts_per_page = (get_option('posts_per_page')) ? get_option('posts_per_page') : 1;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$psc_query = new WP_Query(array(
    'post_type' => 'psc_product',
    'posts_per_page' => $posts_per_page,
    'order' => 'ASC',
    'orderby' => 'title',
    'paged' => $paged
        ));

if ($psc_query->have_posts()) {
    psc_product_loop_start();
    while ($psc_query->have_posts()) : $psc_query->the_post();
        psc_get_template('content-psc_product.php');
    endwhile;

    psc_product_loop_end();
    do_action('psc_after_shop_loop');
    wp_reset_postdata();
} else {
    psc_get_template('loop/psc-no-products-found.php');
}
?> 
