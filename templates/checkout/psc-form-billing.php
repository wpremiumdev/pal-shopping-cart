<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $empty_filed;
$empty_class = "border: 1px solid red";

if (isset($empty_filed['country']) && $empty_filed['country'] == 'empty') {
    ?>
    <style>
        div.psc-custom-select{
            border: 1px solid red;
        }

    </style>
<?php }
?>
<div class="psc-billing-fields">
    <h3><?php echo esc_html("Billing Information"); ?></h3>
    <p class="psc-row psc-row-first " id="billing_first_name_field">
        <label for="billing_first_name" class="">First Name <abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="" value="<?php
        if (isset($empty_filed['first_name']) && $empty_filed['first_name'] != 'empty') {
            echo esc_html($empty_filed['first_name']);
        }
        ?>" style="<?php
               if (isset($empty_filed['first_name']) && $empty_filed['first_name'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">     
    </p>
    <p class="psc-row psc-row-last " id="billing_last_name_field">
        <label for="billing_last_name" class="">Last Name <abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_last_name" id="billing_last_name" placeholder="" value="<?php
        if (isset($empty_filed['last_name']) && $empty_filed['last_name'] != 'empty') {
            echo esc_html($empty_filed['last_name']);
        }
        ?>" style="<?php
               if (isset($empty_filed['last_name']) && $empty_filed['last_name'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
    <p class="psc-row psc-row-wide" id="billing_company_field">
        <label for="billing_company" class="">Company Name</label>
        <input type="text" class="input-text " name="billing_company" id="billing_company" placeholder="" value="">
    </p>
    <p class="psc-row psc-row-first validate-email" id="billing_email_field">
        <label for="billing_email" class="">Email Address <abbr class="required" title="required">*</abbr></label>
        <input type="email" class="input-text " name="billing_email" id="billing_email" placeholder="" value="<?php
        if (isset($empty_filed['email']) && $empty_filed['email'] != 'empty') {
            echo esc_html($empty_filed['email']);
        }
        ?>" style="<?php
               if (isset($empty_filed['email']) && $empty_filed['email'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
    <p class="psc-row psc-row-last" id="billing_phone_field">
        <label for="billing_phone" class="">Phone <abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_phone" id="billing_phone" placeholder="" value="<?php
        if (isset($empty_filed['phone']) && $empty_filed['phone'] != 'empty') {
            echo esc_html($empty_filed['phone']);
        }
        ?>" style="<?php
               if (isset($empty_filed['phone']) && $empty_filed['phone'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
    <p class="psc-row psc-row-dropdown" id="billing_country_field">
        <label for="billing_country" class="">Country 
            <abbr class="required" title="required">*</abbr>
        </label>
        <select name="billing_country" id="billing_country" class="psc-custom-select" >
            <?php
            $psc_country_obj = new PSC_Countries();
            $psc_country = $psc_country_obj->Countries();
            $option = '<option value="select">Select Country</option>';
            foreach ($psc_country as $key => $value) {
                $option .='<option value="' . $key . '">' . $value . '</option>';
            }
            echo $option;
            ?>                  
        </select>
    </p>
    <p class="psc-row psc-row-wide" id="billing_address_1_field" >
        <label for="billing_address_1" class="">Address <abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_address_1" id="billing_address_1" placeholder="Street address" value="<?php
        if (isset($empty_filed['address1']) && $empty_filed['address1'] != 'empty') {
            echo esc_html($empty_filed['address1']);
        }
        ?>" style="<?php
               if (isset($empty_filed['address1']) && $empty_filed['address1'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
    <p class="psc-row psc-row-wide " id="billing_address_2_field">
        <input type="text" class="input-text " name="billing_address_2" id="billing_address_2" placeholder="Apartment, suite, unit etc. (optional)" value="">
    </p>
    <p class="psc-row psc-row-wide " id="billing_city_field" >
        <label for="billing_city" class="">Town / City <abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_city" id="billing_city" placeholder="Town / City" value="<?php
        if (isset($empty_filed['city']) && $empty_filed['city'] != 'empty') {
            echo esc_html($empty_filed['city']);
        }
        ?>" style="<?php
               if (isset($empty_filed['city']) && $empty_filed['city'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
    <p class="psc-row psc-row-first selectd_country_state_display" id="billing_state">
        <label for="billing_state" class="psc_billing_state">State / County 
            <abbr class="required" title="required">*</abbr>
        </label>
        <input type="hidden" class="check_state_form_data" name="check_state_form_data" id="check_state_form_data" value="<?php
        if (isset($empty_filed['state'])) {
            echo esc_html($empty_filed['state']);
        }
        ?>">

    </p>
    <p class="psc-row psc-row-last" id="billing_postcode_field">
        <label for="billing_postcode_field" class="">Postcode / Zip<abbr class="required" title="required">*</abbr></label>
        <input type="text" class="input-text " name="billing_postcode" id="billing_postcode" placeholder="Postcode / Zip" value="<?php
               if (isset($empty_filed['postcode']) && $empty_filed['postcode'] != 'empty') {
                   echo esc_html($empty_filed['postcode']);
               }
               ?>" style="<?php
               if (isset($empty_filed['postcode']) && $empty_filed['postcode'] == 'empty') {
                   echo esc_html($empty_class);
               }
               ?>">
    </p>
</div>