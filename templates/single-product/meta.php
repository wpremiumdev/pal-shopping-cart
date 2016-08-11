<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();
$psc_sku = $PSC_Common_Function->psc_get_sku($post);
$psc_price = $PSC_Common_Function->psc_get_price($post);
$product_stock = $PSC_Common_Function->get_stock_by_post_id($post->ID);
$is_status = $PSC_Common_Function->psc_get_product_status($post);
$is_manage_stock = $PSC_Common_Function->psc_get_manage_stock($post);
$redirect_behaviour = $PSC_Common_Function->add_cart_after_redirect_behaviour();
$add_cart_redirect_behaviour = "";
if( isset($redirect_behaviour) && !empty($redirect_behaviour)){
    $add_cart_redirect_behaviour = esc_url(get_permalink($PSC_Common_Function->add_cart_after_redirect_behaviour()));
}

$css_variable = "";
$product_stock = trim($product_stock);
?>
<div class="psc-single-product-add-to-cart">


    <?php
    if (isset($is_manage_stock) && $is_manage_stock == true) {

        if (isset($is_status) && $is_status == 'instock') {

            if (strlen($product_stock) > 0) {
                if ($product_stock > 0) {
                    $css_variable = "display:block";
                    ?>

                    <input type="number" name="psc_quantity" id="psc_quantity" value="1" min="1" max="<?php echo esc_attr($product_stock); ?>"/>
                    <input type="hidden" name="psc_available_stock" id="psc_available_stock" value="<?php echo esc_attr($product_stock); ?>" />
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" />
                    <a id="psc_add_to_cart_button<?php echo esc_attr($post->ID); ?>" rel="nofollow" psc-product-id="<?php echo esc_attr($post->ID); ?>" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now<?php echo esc_attr($post->ID); ?>" hidden=""></span> </a>
                    <a href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())); ?>" class="view_cart<?php echo esc_attr($post->ID); ?> view_cart" title="View Cart" hidden>View Cart</a>
                    <input type="hidden" id="add_cart_after_redirect_behaviour" value="<?php echo $add_cart_redirect_behaviour; ?>">    

                    <?php
                } else {
                    $css_variable = "display:none";
                    ?>

                    <input type="number" name="psc_quantity" id="psc_quantity" value="0" disabled/>
                    <input type="hidden" name="psc_available_stock" id="psc_available_stock" value="<?php echo esc_url($product_stock); ?>" />
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" />
                    <a id="psc_add_to_cart_button<?php echo esc_attr($post->ID); ?>" rel="nofollow" psc-product-id="<?php echo esc_attr($post->ID); ?>" class="psc-button psc_add_to_cart_button product_type_simple" style="pointer-events: none;cursor: default; background-color:#E8E8E8">Add to cart <span class="pac_add_to_cart_process process_now<?php echo esc_attr($post->ID); ?>" hidden=""></span> </a>        
                    <a href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())); ?>" class="view_cart<?php echo esc_attr($post->ID); ?> view_cart" title="View Cart" hidden>View Cart</a>
                    <input type="hidden" id="add_cart_after_redirect_behaviour" value="<?php echo $add_cart_redirect_behaviour; ?>"> 

                    <?php
                }
            } else {
                $css_variable = "display:block";
                ?>

                <input type="number" name="psc_quantity" id="psc_quantity" value="1" min="1" max="<?php echo esc_attr($product_stock); ?>"/>
                <input type="hidden" name="psc_available_stock" id="psc_available_stock" value="<?php echo esc_attr($product_stock); ?>" />
                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" />
                <a id="psc_add_to_cart_button<?php echo esc_attr($post->ID); ?>" rel="nofollow" psc-product-id="<?php echo esc_attr($post->ID); ?>" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now<?php echo esc_attr($post->ID); ?>" hidden=""></span> </a>
                <a href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())); ?>" class="view_cart<?php echo esc_attr($post->ID); ?> view_cart" title="View Cart" hidden>View Cart</a>
                <input type="hidden" id="add_cart_after_redirect_behaviour" value="<?php echo $add_cart_redirect_behaviour; ?>">    

                <?php
            }
        } else if (isset($is_status) && $is_status == 'outofstock') {
            $css_variable = "display:none";
            ?>

            <input type="number" name="psc_quantity" id="psc_quantity" value="0" disabled/>
            <input type="hidden" name="psc_available_stock" id="psc_available_stock" value="<?php echo esc_url($product_stock); ?>" />
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" />
            <a id="psc_add_to_cart_button<?php echo esc_attr($post->ID); ?>" rel="nofollow" psc-product-id="<?php echo esc_attr($post->ID); ?>" class="psc-button psc_add_to_cart_button product_type_simple" style="pointer-events: none;cursor: default; background-color:#E8E8E8">Add to cart <span class="pac_add_to_cart_process process_now<?php echo esc_attr($post->ID); ?>" hidden=""></span> </a>        
            <a href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())); ?>" class="view_cart<?php echo esc_attr($post->ID); ?> view_cart" title="View Cart" hidden>View Cart</a>
            <input type="hidden" id="add_cart_after_redirect_behaviour" value="<?php echo $add_cart_redirect_behaviour; ?>"> 

            <?php
        }
    } else {

        $css_variable = "display:block";
        ?>

        <input type="number" name="psc_quantity" id="psc_quantity" value="1" min="1" max="<?php echo esc_attr($product_stock); ?>"/>
        <input type="hidden" name="psc_available_stock" id="psc_available_stock" value="<?php echo esc_attr($product_stock); ?>" />
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($post->ID); ?>" />
        <a id="psc_add_to_cart_button<?php echo esc_attr($post->ID); ?>" rel="nofollow" psc-product-id="<?php echo esc_attr($post->ID); ?>" class="psc-button psc_add_to_cart_button product_type_simple">Add to cart <span class="pac_add_to_cart_process process_now<?php echo esc_attr($post->ID); ?>" hidden=""></span> </a>
        <a href="<?php echo esc_url(get_permalink($PSC_Common_Function->psc_addtocart_after_redirect_page())); ?>" class="view_cart<?php echo esc_attr($post->ID); ?> view_cart" title="View Cart" hidden>View Cart</a>
        <input type="hidden" id="add_cart_after_redirect_behaviour" value="<?php echo $add_cart_redirect_behaviour; ?>">    

        <?php
    }
    ?>

</div>
    <?php
    $is_result = $PSC_Common_Function->enable_paypal_express_checkout_button();
    if ( $PSC_Common_Function->get_cart_total_is_empty() && $is_result) {
    ?>
    <div class="psc-enable_express-checkout" style="<?php echo $css_variable; ?>">    
        <?php
        $p_name_title = ($post->post_name) ? $post->post_name : $post->post_title;
        do_action('enable_checkout_button', 'single', $p_name_title);    
    ?> 
</div>
    <?php
}
if ($psc_sku) : ?>
<div class="product_meta">
    <?php do_action('psc_product_meta_start'); ?>
        <span class="sku_wrapper"><?php echo esc_html('SKU:', 'pal-shopping-cart'); ?> 
            <span class="sku" itemprop="sku"><?php echo ( $psc_sku ) ? esc_html($psc_sku) : esc_html('N/A', 'pal-shopping-cart'); ?></span>
        </span>
    <?php do_action('psc_product_meta_end'); ?>
    </div>
<?php endif;