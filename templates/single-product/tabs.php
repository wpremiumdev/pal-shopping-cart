<?php
if (!defined('ABSPATH')) {
    exit;
}
global $post;
$post_content = get_post_field('post_content', $post->ID);
$post_content = trim(str_replace('[display_product]', '', $post_content));
if (!empty($post_content)) :
    ?>
    <div class="psc-content psc-content-wrapper">
        <ul class="psc-single-product-tabs-menu">
            <li class="current">
                <a>
                    <?php echo esc_html('Product Description'); ?>
                </a>
            </li>        
        </ul>
        <div class="psc-single-product-tabs">
            <div id="psc-single-product-tabs-1" class="psc-single-product-tabs-content">
                <?php 
                     echo do_shortcode($post_content);
                ?>
            </div>
        </div>
    </div>

<?php endif; ?>