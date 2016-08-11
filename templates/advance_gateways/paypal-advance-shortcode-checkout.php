<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if( (isset($_GET['order-pay']) && !empty($_GET['order-pay'])) && (isset($_GET['psc-order-pay']) && 'Receipt' == $_GET['psc-order-pay']) ){    
    if (!class_exists('Paypal_Shopping_Cart_PayPal_Advanced')) {
            require_once( PAYPAL_FOR_PAYPAL_SHOPPING_CART_PLUGIN_DIR . '/public/partials/paypal-shopping-cart-paypal-advanced.php' );
        }
    $Paypal_Shopping_Cart_PayPal_Advanced = new Paypal_Shopping_Cart_PayPal_Advanced();
    $Paypal_HTML = $Paypal_Shopping_Cart_PayPal_Advanced->pal_shopping_cart_paypal_advanced_order_HTML($_GET['order-pay']);
    ?>
    <script type="text/javascript">
        jQuery('header h1.entry-title').html();
        jQuery('header h1.entry-title').html('Pay For Order');
    </script>
    <div class="pal_shopping_cart_pay_for_order"> 
    <?php
    echo $Paypal_HTML;
    do_action('pal_shopping_cart_get_receipt_hook', $_GET);
    ?>
</div>    
<?php }