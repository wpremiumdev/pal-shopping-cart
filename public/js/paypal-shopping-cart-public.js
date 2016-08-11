jQuery(function($) {

    jQuery(".zoom").colorbox({rel: 'zoom', transition: "fade"});

    jQuery('body').addClass('paypal-shopping-carts');

    var check_state_form_data = jQuery('#check_state_form_data').val();

    if (check_state_form_data == 'empty') {
        jQuery('.psc_billing_state').after('<input type="text" name="billing_state" id="billing_state" class="input-text billing_state" style="border: 1px solid red" placeholder="State / Country" /> ');
    } else {
        jQuery('.psc_billing_state').after('<input type="text" name="billing_state" id="billing_state" class="input-text billing_state" placeholder="State / Country" />');
    }

    jQuery(document).on('click', '.direct_payment_to_cart_product', function(e) {
        var base_url = base_plugin_url + 'public/images/paypal-loader.gif';
        jQuery('.psc-proceed-to-checkout').block({message: '<img src="' + base_url + '"/>', overlayCSS: {background: '#ededed', opacity: 0.6}});
    });

    jQuery(document).on('click', '.direct_payment_to_checkout_product', function(e) {
        var base_url = base_plugin_url + 'public/images/paypal-loader.gif';
        jQuery('.psc-enable_express-checkout').block({message: '<img src="' + base_url + '"/>', overlayCSS: {background: '#ededed', opacity: 0.6}});
    });

    jQuery(document).on('click', '.direct_payment_to_single_product', function(e) {
        var herf_link = $(this).attr('href');
        Direct_Addtocart_and_Payment(herf_link, e);
    });

    function Direct_Addtocart_and_Payment(psc_url, e) {

        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }

        var base_url = base_plugin_url;
        jQuery('.psc-entry-summary').block({message: '<img src="' + base_url + 'public/images/paypal-loader.gif"/>', overlayCSS: {background: '#fff', opacity: 0.6}});
        var is_variable_product_result = is_variable_product();
        var get_attr_id = jQuery('.psc-single-product-add-to-cart a.product_type_simple').attr('psc-product-id');
        var psc_qty = jQuery('#psc_quantity').val();
        var psc_available = jQuery('#psc_available_stock').val();
        if (typeof psc_qty === 'undefined') {
            psc_qty = 1;
        }

        if (parseInt(psc_qty) > parseInt(psc_available)) {
            psc_qty = 1;
        } else if (psc_qty == "" || parseInt(psc_qty) == 0) {
            return false;
        }

        var get_attr_action = 'insert';
        var array_data = [];
        array_data[0] = get_attr_id;
        array_data[1] = get_attr_action;
        array_data[2] = psc_qty;

        if (is_variable_product_result) {
            array_data[3] = jQuery('.psc-product-details-all #psc_select_variable_product').val();
        }

        var data = {
            action: 'psc_add_to_cart_item',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: array_data
        };

        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {

            jQuery('.psc-entry-summary').unblock();
            if (response == 'success')
            {
                $(location).attr('href', psc_url);
            }

        });
        e.preventDefault();
    }

    jQuery(document).on('click', '.psc_add_to_cart_button', function(e) {
        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }

        jQuery('#psc_quantity').css('border', '1px solid #ddd');
        var is_variable_product_result = is_variable_product();

        var get_attr_id = jQuery(this).attr('psc-product-id');
        jQuery('#psc_add_to_cart_button' + get_attr_id).block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});
        var psc_qty = jQuery('#psc_quantity').val();
        var psc_available = jQuery('#psc_available_stock').val();

        var redirect_behaviour = jQuery('#add_cart_after_redirect_behaviour').val();

        if (typeof psc_qty === 'undefined') {
            psc_qty = 1;
        }

        if ((psc_available == "nolimit") && parseInt(psc_qty) > parseInt(psc_available)) {
            jQuery('#psc_add_to_cart_button' + get_attr_id).unblock();
            jQuery('#psc_quantity').css('border', '1px solid #a44');
            return false;

        } else if ((psc_available == "nolimit") && psc_qty == "" || parseInt(psc_qty) == 0) {
            jQuery('#psc_add_to_cart_button' + get_attr_id).unblock();
            jQuery('#psc_quantity').css('border', '1px solid #a44');
            return false;
        }

        var get_attr_action = 'insert';
        var array_data = [];
        array_data[0] = get_attr_id;
        array_data[1] = get_attr_action;
        array_data[2] = psc_qty;

        if (is_variable_product_result) {
            array_data[3] = jQuery('.psc-product-details-all #psc_select_variable_product').val();
        }

        var data = {
            action: 'psc_add_to_cart_item',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: array_data
        };

        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
            jQuery('#psc_add_to_cart_button' + get_attr_id).unblock();

            if (response == 'success') {
                if (typeof redirect_behaviour === 'undefined') {
                    jQuery('.view_cart' + get_attr_id).show();
                    jQuery('.view_cart' + get_attr_id).css('display', 'block');
                    jQuery('.qty_less_then_stock_error').css('display', 'none');

                } else if (redirect_behaviour.toString().length > 0) {
                    $(location).attr("href", redirect_behaviour);
                } else {
                    jQuery('.view_cart' + get_attr_id).show();
                    jQuery('.view_cart' + get_attr_id).css('display', 'block');
                    jQuery('.qty_less_then_stock_error').css('display', 'none');
                }
            } else {
                var json_data = $.parseJSON(response);
                var stock_empty = json_data.MSG;
                if (stock_empty.toString().length > 0 && stock_empty == 'stock_empty') {
                    $(location).attr("href", json_data.URL);
                }
            }
        });
        e.preventDefault();
        return false;
    });

    jQuery(document).on('click', '.psc-product-remove-icon', function(e) {
        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }
        var get_attr_id = jQuery(this).attr('data-row-id');
        var get_attr_action = 'delete';
        var array_data = [];

        array_data[0] = get_attr_id;
        array_data[1] = get_attr_action;

        var data = {
            action: 'psc_add_to_cart_item',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: array_data
        };

        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
            location.reload();
        });
    });

    jQuery(document).on('click', '#psc_update_cart', function(e) {

        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }
        var rowCount = $('.psc_shop_table tr').length;
        jQuery('#psc_update_item_qty').css('border', '1px solid #ddd');
        jQuery('#psc_update_cart').block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});


        var array_data_update = {};
        var p = 0;
        for (var i = 1; i < rowCount - 1; i++) {

            var array_data = {};
            var rowid = $('.psc-product-tr-' + i + ' td.psc-product-remove span').attr('data-row-id');
            var id = $('.psc-product-tr-' + i + ' td.psc-product-remove span').attr('data-product-id');
            var name = $('.psc-product-tr-' + i + ' td.psc-product-name strong').attr('data-variation-name');
            var qty = $('.psc-product-tr-' + i + ' td.psc-product-quantity input').val();

            if (qty == "" || parseInt(qty) == 0) {
                jQuery('#psc_update_cart').unblock();
                jQuery('#psc_update_item_qty').css('border', '1px solid #a44');
                return false;
            }

            array_data['rowid'] = rowid;
            array_data['id'] = id;
            array_data['name'] = name;
            array_data['qty'] = qty;
            array_data_update[p] = array_data;
            p++;
        }
        var data = {
            action: 'psc_update_cart_item',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: array_data_update
        };

        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
            set_update_cart_session_result(response);
        });
    });

    jQuery(document).on('change', '#billing_country', function() {
        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }
        var get_attr_action = 'billing_state';
        var selected_country_code = jQuery('#billing_country').val();
        var array_data = [];
        array_data[0] = get_attr_action;
        array_data[1] = selected_country_code;

        var data = {
            action: 'psc_update_country_state',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: array_data
        };
        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
            if (response == "nofound") {
                jQuery('input#billing_state').remove();
                jQuery('#billing_state div.psc-custom-select').remove();
                var str_data = '<input type="text" name="billing_state" id="billing_state" class="input-text billing_state" placeholder="State / Country" />';
                jQuery('.psc_billing_state').after(str_data);
            } else {
                jQuery('input#billing_state').remove();
                jQuery('p#billing_state div.psc-custom-select').remove();
                jQuery('.psc_billing_state').after($.parseJSON(response));
                jQuery(".paypal-shopping-carts-checkout form#psc_checkout_process_now #psc_customer_billing_details select[name='billing_state']").customselect();
            }
        });
    });

    jQuery(document).on('click', '#psc_applay_coupons', function() {

        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }

        var coupon_code = jQuery('#psc_applay_coupons_text').val();
        if (coupon_code.toString().length > 0) {

            jQuery('#psc_applay_coupons').block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});

            var data = {
                action: 'psc_update_cart_with_coupon',
                security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
                value: coupon_code
            };
            jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
                jQuery('#psc_applay_coupons').unblock();
                jQuery('#psc_applay_coupons_text').val('');               
                window.location.href = cart_page; 
            });
        } else {
            jQuery('.psc_shop_table_div .psc_display_notice').html('');
            jQuery('.psc_shop_table_div .psc_display_notice').append('<div class="psc-alert-box psc-notice">Please Enter Coupons Code.</div>');
        }
    });

    jQuery(document).on('change', '#psc_select_variable_product', function() {

        if (typeof paypal_shopping_cart_url_params === 'undefined') {
            return false;
        }


        $('div.psc-entry-summary').block({message: null, overlayCSS: {background: '#fff', opacity: 0.6}});
        var select_val = jQuery('#psc_select_variable_product').val();
        var select_id = jQuery('#psc_select_variable_product').attr('product-id');
        var array_data = [];
        array_data[0] = select_val;
        array_data[1] = select_id;
        if (select_val.toString().length > 0) {
            var data = {
                action: 'psc_select_variable_product',
                security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
                value: array_data
            };
            jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
                var res = $.parseJSON(response);
                jQuery('.psc-product-details-price').html('');
                jQuery('.psc-product-details-price').html(res.product_details);
                jQuery('.psc-single-product-add-to-cart').html('');
                jQuery('.psc-single-product-add-to-cart').html(res.product_addcart);
                if (res.product_stock) {
                    jQuery('.psc-enable_express-checkout').show();
                } else {
                    jQuery('.psc-enable_express-checkout').hide();
                }
                $('div.psc-entry-summary').unblock();
            });
        }
    });

    jQuery(document).on('click', '.class_to_defualt_cursor', function(e) {
        jQuery(this).css('background-color', '#E8E8E8');
        jQuery(this).css('pointer-events', 'none');
        jQuery(this).css('cursor', 'default');
    });

    jQuery(".paypal-shopping-carts-checkout form#psc_checkout_process_now #psc_customer_billing_details #billing_country").customselect();

    jQuery(document).on('keypress', '#pro_card_number,#pro_card_ex_mm,#pro_card_ex_yy,#pro_card_cvc,#card_number,#card_ex_mm,#card_ex_yy,#card_cvc', function (e) {

        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
        var name = $(this).attr('data-name');
        if (name == 'number') {
            if ($(this).val().length >= '16') {
                return false;
            }
        } else if (name == 'ex_mm') {
            if ($(this).val().length >= '2') {
                return false;
            }
        } else if (name == 'ex_yy' || name == 'cvc') {
            if ($(this).val().length >= '4') {
                return false;
            }
        }
    });

    function is_variable_product() {

        var is_variable = jQuery('.psc-product-details-all #psc_select_variable_product').val();
        if (typeof is_variable === 'undefined') {
            return false;
        } else {
            return true;
        }
    }

    function set_update_cart_session_result(response) {

        var data = {
            action: 'set_update_cart_session_result',
            security: paypal_shopping_cart_url_params.paypal_shopping_cart_url,
            value: response
        };

        jQuery.post(paypal_shopping_cart_url_params.ajax_url, data, function(response) {
            jQuery('#psc_update_cart').unblock();
            location.reload();
        });
    }
});