<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;

$PSC_Common_Function = new PSC_Common_Function();
$currency_code = $PSC_Common_Function->get_psc_currency();
$currency_symbol = $PSC_Common_Function->get_psc_currency_symbol($currency_code);
$result_array = $PSC_Common_Function->get_post_meta_all($post->ID);
$psc_product_sku = '';
$psc_regular_price = '';
$psc_sale_price = '';
$selected_option = '';
$psc_sale_tax = '';
$psc_sale_ship = '';
$product_type = 'simple';
$product_stock_simple = 'instock';
$product_stock_variable = 'instock';
$psc_variable_product_data = array();
if (isset($result_array['_psc_sku']) && !empty($result_array['_psc_sku'])) {
    $psc_product_sku = $result_array['_psc_sku'];
}
if (isset($result_array['_psc_regular_price']) && !empty($result_array['_psc_regular_price'])) {
    $psc_regular_price = $result_array['_psc_regular_price'];
}
if (isset($result_array['_psc_sale_price']) && !empty($result_array['_psc_sale_price'])) {
    $psc_sale_price = $result_array['_psc_sale_price'];
}

if (isset($result_array['_psc_sale_tax']) && !empty($result_array['_psc_sale_tax'])) {
    $psc_sale_tax = $result_array['_psc_sale_tax'];
}

if (isset($result_array['_psc_sale_ship']) && !empty($result_array['_psc_sale_ship'])) {
    $psc_sale_ship = $result_array['_psc_sale_ship'];
}

if (isset($result_array['psc-product-type-dropdown']) && !empty($result_array['psc-product-type-dropdown'])) {
    $product_type = $result_array['psc-product-type-dropdown'];
}
if (isset($result_array['_psc_stock_status_simple']) && !empty($result_array['_psc_stock_status_simple'])) {
    $product_stock_simple = $result_array['_psc_stock_status_simple'];
}
if (isset($result_array['_psc_stock_status_variable']) && !empty($result_array['_psc_stock_status_variable'])) {
    $product_stock_variable = $result_array['_psc_stock_status_variable'];
}
if (isset($result_array['psc_variable_product_data']) && !empty($result_array['psc_variable_product_data'])) {
    $psc_variable_product_data =  $PSC_Common_Function->get_unserialize_data($result_array['psc_variable_product_data']);
}

$product_type_arrray = array("simple" => "Simple Product", "variable" => "Variable Product");
?>
<div class="wrap" id="">
    <table class="widefat" cellspacing="0">                        
        <tbody>
            <tr>                        
                <td >
                    <input type="hidden" id="product_sku" value="<?php echo esc_html($psc_product_sku); ?>">
                    <select class="psc-product-type-dropdown" name="psc-product-type-dropdown" id="psc-product-type-dropdown">
                        <optgroup label="Product Type">
                            <?php
                            foreach ($product_type_arrray as $key => $value) {
                                $selected = '';
                                if ($key == $product_type) {
                                    $selected = 'selected';
                                }
                                $selected_option .= "<option value='$key' $selected>" . $value . "</option>";
                            }
                            echo $selected_option;
                            ?>                            
                        </optgroup>
                    </select>
                </td>
            </tr>

        </tbody>
    </table>
    <div id="psc_simpale_product_div" hidden>
        <ul>
            <li tab="tab1" class="first current"><?php _e('General', 'pal-shopping-crt') ?></li>  
            <li tab="tab2" class="last"><?php _e('Inventory', 'pal-shopping-crt') ?></li>    
        </ul>       
        <div class="tab-content" style="display: block;">
            <table class="widefat" cellspacing="0" style="clear: inherit;">                  
                <tbody >
                    <tr class="product_sku">   

                    </tr>
                    <tr>   
                        <th><?php echo _e('Regular Price', 'pal-shopping-crt').' ('.$currency_symbol.') '; ?></th>
                        <td > 
                            <input type="text" class="psc_input_regulare_price" name="_psc_regular_price" id="_psc_regular_price" value="<?php echo esc_attr($psc_regular_price); ?>" placeholder="">

                        </td>
                    </tr>
                    <tr>   
                        <th><?php echo _e('Sale Price', 'pal-shopping-crt').' ('.$currency_symbol.') '; ?></th>
                        <td > 
                            <input type="text" class="psc_input_sale_price" name="_psc_sale_price" id="_psc_sale_price" value="<?php echo esc_attr($psc_sale_price); ?>" placeholder="">
                        </td>
                    </tr>
                    <tr>   
                        <th><?php echo _e('TAX', 'pal-shopping-crt').' (%) '; ?></th>
                        <td > 
                            <input type="text" class="psc_input_sale_tax" name="_psc_sale_tax" id="_psc_sale_tax" value="<?php echo esc_attr($psc_sale_tax); ?>" placeholder="TAX(%)">
                        </td>
                    </tr>
                    <tr>   
                        <th><?php echo _e('Shipping', 'pal-shopping-crt').' ('.$currency_symbol.') '; ?></th>
                        <td > 
                            <input type="text" class="psc_input_sale_ship" name="_psc_sale_ship" id="_psc_sale_ship" value="<?php echo esc_attr($psc_sale_ship); ?>" placeholder="Shipping">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>  
        <div class="tab-content">
            <table class="widefat" cellspacing="0" style="clear: inherit; min-height: 60px;">                  
                <tbody >                    
                    <tr>   
                        <th> <?php _e('Manage stock?', 'pal-shopping-crt') ?></th>
                        <td style="padding-top: 20px;">                             
                            <input type="checkbox" class="checkbox" name="_psc_manage_stock_simple" id="_psc_manage_stock_simple" value="<?php echo isset($result_array['_psc_manage_stock_simple']) ? esc_attr($result_array['_psc_manage_stock_simple']) : esc_attr('no') ?>" <?php
                            if (isset($result_array['_psc_manage_stock_simple']) && $result_array['_psc_manage_stock_simple'] == 'yes') {
                                echo esc_html('checked');
                            }
                            ?> />
                            <span><?php echo esc_html('Enable stock management.') ?></span>
                        </td>
                    </tr>                    
                </tbody>
            </table>        
            <table class="widefat" cellspacing="0" id="_psc_simple_product_stock_table" hidden="" style="display: none;">                  
                <tbody >
                    <tr>   
                        <th><?php _e('Stock Qty', 'pal-shopping-crt') ?></th>
                        <td > 
                            <input type="text" class="psc_input_sale_qty" name="_psc_stock_qty_simple" id="_psc_stock_qty_simple" value="<?php echo isset($result_array['_psc_stock_qty_simple']) ? esc_attr($result_array['_psc_stock_qty_simple']) : '' ?>">
                        </td>
                    </tr>
                    <tr>   
                        <th><?php _e('Stock Status', 'pal-shopping-crt') ?></th>
                        <td >                             
                            <select class="psc-product-type-dropdown" name="_psc_stock_status_simple" id="_psc_stock_status_simple">
                                <option value="instock" <?php
                                if ($product_stock_simple == 'instock') {
                                    echo esc_attr('selected');
                                }
                                ?>><?php _e('In stock', 'pal-shopping-crt') ?></option> 
                                <option value="outofstock" <?php
                                if ($product_stock_simple == 'outofstock') {
                                    echo esc_attr('selected');
                                }
                                ?>><?php _e('Out of stock', 'pal-shopping-crt') ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>         
    <div class="wrap" id="psc_variable_product_div" hidden>
        <ul>
            <li tab="tab1" class="first current"><?php _e('General', 'pal-shopping-crt') ?></li>                        
            <li tab="tab2" class="last"><?php _e('Inventory', 'pal-shopping-crt') ?></li>
        </ul>
        <div class="tab-content" style="display: block;">
            <table class="widefat" cellspacing="0" style="clear: inherit; min-height: 90px;">                       
                <tbody>
                    <tr class="product_sku">   

                    </tr>                                                            
                </tbody>
            </table>
        </div>
        <div class="tab-content">           
            <table id="psc_variable_product" class="widefat" data-custom="0"> 
                <th><?php _e('Variation Name', 'pal-shopping-crt') ?></th>
                <th><?php _e('Regular Price('.$currency_symbol.')', 'pal-shopping-crt') ?></th>
                <th><?php _e('Sale Price('.$currency_symbol.')', 'pal-shopping-crt') ?></th>
                <th><?php _e('Manage Stock', 'pal-shopping-crt') ?></th>
                <th><?php _e('IN / OUT Stock', 'pal-shopping-crt') ?></th>
                <th class="_psc_manage_stock_variable_display"><?php _e('QTY', 'pal-shopping-crt') ?></th>
                <th><?php _e('TAX(%)', 'pal-shopping-crt') ?></th>
                <th><?php _e('Shipping('.$currency_symbol.')', 'pal-shopping-crt') ?></th>
                <th><?php _e('Action', 'pal-shopping-crt') ?></th>

                <?php if (isset($result_array['psc_variable_product_count']) && count($psc_variable_product_data) > 0) { ?>
                    <input type = "hidden" class="input input_box" name = "psc_variable_product_count" id = "psc_product_count" value="<?php echo esc_attr($result_array['psc_variable_product_count']); ?>">
                    <?php
                    foreach ($psc_variable_product_data as $key => $value) {
                        $psc_variable_product_stock_status = ($value['psc_variable_product_stock_status' . $key]) ? $value['psc_variable_product_stock_status' . $key] : 0;
                        $psc_manage_stock_variable = ($value['_psc_manage_stock_variable' . $key]) ? $value['_psc_manage_stock_variable' . $key] : 0;
                        ?>
                        <tr id="psc_table_option_<?php echo esc_attr($key); ?>" data-tr="<?php echo esc_attr($key); ?>">                                         
                            <td>
                                <input type = "text" class="psc_input input_box" name = "psc_variable_product_name<?php echo esc_attr($key); ?>" id = "psc_product_name<?php echo esc_attr($key); ?>" placeholder = "Variation Name" value="<?php echo esc_attr($value['psc_variable_product_name' . $key]); ?>">

                            </td>
                            <td>
                                <input type = "text" class="psc_input_regulare_price psc_input input_box" name = "psc_variable_product_regular_price<?php echo esc_attr($key); ?>" id = "psc_product_regular_price<?php echo esc_attr($key); ?>" placeholder = "Regular Price" value="<?php echo esc_attr($value['psc_variable_product_regular_price' . $key]); ?>">                                            
                            </td>
                            <td>
                                <input type = "text" class="psc_input_sale_price psc_input input_box" name = "psc_variable_product_sale_price<?php echo esc_attr($key); ?>" id = "psc_product_sale_price<?php echo esc_attr($key); ?>" data-id="<?php echo esc_attr($key); ?>" placeholder = "Sale Price" value="<?php echo esc_attr($value['psc_variable_product_sale_price' . $key]); ?>">                                            
                            </td>                    
                            <td>
                                <input type = "checkbox" class="psc_input_checkbox input_box" name = "_psc_manage_stock_variable<?php echo esc_attr($key); ?>" id = "psc_product_manage<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($psc_manage_stock_variable); ?>" <?php if ($psc_manage_stock_variable == '1') {
                            echo esc_html('checked');
                        } ?>>                                            
                            </td>
                            <td>
                                <input type = "checkbox" class="psc_input_checkbox input_box" name = "psc_variable_product_stock_status<?php echo esc_attr($key); ?>" id = "psc_variable_product_stock_status<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($psc_variable_product_stock_status); ?>" <?php if ($psc_variable_product_stock_status == '1') {
                            echo esc_html('checked');
                        } ?>>                                            
                            </td>
                            <td>
                                <input type = "text" class="psc_input input_box psc_input_sale_qty" name = "psc_variable_product_stock<?php echo esc_attr($key); ?>" id = "psc_product_stock<?php echo esc_attr($key); ?>" placeholder = "QTY" value="<?php echo esc_attr($value['psc_variable_product_stock' . $key]); ?>">                                            
                            </td>                    
                            <td>
                                <input type = "text" class="psc_input input_box psc_input_sale_tax" name = "psc_variable_product_tax<?php echo esc_attr($key); ?>" id = "psc_product_tax<?php echo esc_attr($key); ?>" placeholder = "TAX(%)" value="<?php echo esc_attr($value['psc_variable_product_tax' . $key]); ?>">
                            </td>
                            <td>
                                <input type = "text" class="psc_input input_box psc_input_sale_ship" name = "psc_variable_product_ship<?php echo esc_attr($key); ?>" id = "psc_product_ship<?php echo esc_attr($key); ?>" placeholder = "Shipping" value="<?php echo esc_attr($value['psc_variable_product_ship' . $key]); ?>">
                            </td>
                            
                            <td>
                                <?php if ($key == 0) { ?>
                                    <button class="btn button psc_add" id="psc_add_new_variable_product">Add</button>
        <?php } else { ?>
                                    <button class="btn button psc_cancel" id="psc_remove_variable_product">X</button>
                        <?php }
                        ?>

                            </td>
                        </tr>

    <?php
    }
} else {
    ?>
                    <input type = "hidden" class="input input_box" name = "psc_variable_product_count" id = "psc_product_count" value="0">
                    <tr id="psc_table_option_0" data-tr="0">                    
                        <td>
                            <input type = "text" class="psc_input input_box" name = "psc_variable_product_name0" id = "psc_product_name0" placeholder = "Variation Name" value="">                                
                        </td>
                        <td>
                            <input type = "text" class="psc_input_regulare_price psc_input input_box" name = "psc_variable_product_regular_price0" id = "psc_product_regular_price0" placeholder = "Regular Price" value="">                                            
                        </td>
                        <td>
                            <input type = "text" class="psc_input_sale_price psc_input input_box" name = "psc_variable_product_sale_price0" id = "psc_product_sale_price0" data-id="0" placeholder = "Sale Price" value="">                                            
                        </td>                    
                        <td>
                            <input type = "checkbox" class="psc_input_checkbox input_box" name = "_psc_manage_stock_variable0" id = "psc_product_manage0" value="0" >                                            
                        </td>
                        <td>
                            <input type = "checkbox" class="psc_input_checkbox input_box" name = "psc_variable_product_stock_status0" id = "psc_variable_product_stock_status0" value="0" >                                            
                        </td>
                        <td>
                            <input type = "text" class="psc_input input_box psc_input_sale_qty" name = "psc_variable_product_stock0" id = "psc_product_stock0" placeholder = "QTY" value="">                                            
                        </td>                    
                        <td>
                            <input type = "text" class="psc_input input_box psc_input_sale_tax" name = "psc_variable_product_tax0" id = "psc_product_tax0" placeholder = "TAX(%)" value="">                                            
                        </td>
                        <td>
                            <input type = "text" class="psc_input input_box psc_input_sale_ship" name = "psc_variable_product_ship0" id = "psc_product_ship0" placeholder = "Shipping" value="">                                            
                        </td>
                        <td>
                            <button class="btn button psc_add" id="psc_add_new_variable_product">Add</button>
                        </td>
                    </tr>

<?php }
?>
            </table>              
        </div>
    </div>
</div>