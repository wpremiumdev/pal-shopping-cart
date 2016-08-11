<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$type = 'psc_product';
$args = array('post_type' => $type);
$my_query = null;
$my_query = new WP_Query($args);
?>
<div id="psc-product-cat">
    <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <h1>
            <?php echo esc_html(get_the_title()); ?>
        </h1>
        <p id="psc-product-discription">
            <?php echo the_content(); ?>
        </p>
    </div>
<?php endwhile; ?>