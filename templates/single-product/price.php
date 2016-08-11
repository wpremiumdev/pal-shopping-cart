<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$PSC_Common_Function = new PSC_Common_Function();
?>
<div class="psc-product-details-all">

    <?php
    echo $PSC_Common_Function->psc_get_price_variable_and_simple($post);
    ?>

</div>