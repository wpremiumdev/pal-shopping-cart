<?php
if (!defined('ABSPATH')) {
    exit;
}
global $post;
?>

<?php do_action('psc_before_add_to_cart_form'); ?>

<form class="psc-cart" method="post" enctype='multipart/form-data'>

    <input type="text" name="psc-quantity" value="1" />
    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->id); ?>" />

    <button type="submit" class="psc_single_add_to_cart_button button alt"><?php echo esc_html('Add to cart'); ?></button>

    <?php do_action('psc_after_add_to_cart_button'); ?>
</form>

<?php do_action('psc_after_add_to_cart_form'); ?>