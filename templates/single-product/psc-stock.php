<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<?php
global $post;
$PSC_Common_Function = new PSC_Common_Function();
$is_enable_stock_management = $PSC_Common_Function->is_enable_stock_management($post);
$is_variation = $PSC_Common_Function->psc_get_product_type($post);
if ($is_variation == "variable") {
    return;
}
if ($is_enable_stock_management == true) {

    $product_stock = $PSC_Common_Function->psc_get_product_stock($post);
    $is_variation = $PSC_Common_Function->psc_get_product_type($post);
    $is_status = $PSC_Common_Function->psc_get_product_status($post);

    if (isset($product_stock) && $product_stock > 0 && $is_variation == "simple") {
        if (isset($is_status) && $is_status == "instock") {
            ?>
            <p class="stock in-stock"><?php echo esc_html($product_stock . ' In stock'); ?></p>
        <?php } else if (isset($is_status) && $is_status == "outofstock") { ?>
            <p class="stock out-of-stock"><?php echo esc_html('Out of Stock'); ?></p>
            <?php
        }
    } else if (isset($product_stock) && $product_stock == 0 && $is_variation == "simple") {

        $product_stock = trim($product_stock);

        if (isset($is_status) && $is_status == "instock") {

            if (strlen($product_stock) > 0) {

                if ($product_stock > 0) {
                    ?>
                    <p class="stock in-stock"><?php echo esc_html('In stock'); ?></p>    
                    <?php
                } else {
                    ?>
                    <p class="stock out-of-stock"><?php echo esc_html('Out of Stock'); ?></p>    
                    <?php
                }
            } else {
                ?>
                <p class="stock in-stock"><?php echo esc_html('In stock'); ?></p>    
                <?php
            }
        } else if (isset($is_status) && $is_status == "outofstock") {
            ?>
            <p class="stock out-of-stock"><?php echo esc_html('Out of Stock'); ?></p>    
            <?php
        }
    }
}
?>