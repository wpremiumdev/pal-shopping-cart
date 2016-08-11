<?php

if (!defined('ABSPATH')) {
    exit;
}

function template_path() {
    return apply_filters('psc_template_path', 'paypal-shipping-cart/');
}

function psc_get_template_part($slug, $name = '') {
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php
    if ($name) {
        $template = locate_template(array("{$slug}-{$name}.php", template_path() . "{$slug}-{$name}.php"));
    }
    // Get default slug-name.php
    if (!$template && $name && file_exists(PSC_PLUGIN_DIR_PATH . "/templates/{$slug}-{$name}.php")) {
        $template = PSC_PLUGIN_DIR_PATH . "/templates/{$slug}-{$name}.php";
    }
    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php
    if (!$template) {
        $template = locate_template(array("{$slug}.php", PSC_PLUGIN_DIR_PATH . 'templates/' . "{$slug}.php"));
    }
    // Allow 3rd party plugin filter template file from their plugin
    if (!$template || $template) {
        $template = apply_filters('psc_get_template_part', $template, $slug, $name);
    }
    if ($template) {
        load_template($template, false);
    }
}

function psc_get_template($template_name, $args = array(), $template_path = '', $default_path = '') {
    if ($args && is_array($args)) {
        extract($args);
    }
    $located = psc_locate_template($template_name, $template_path, $default_path);
    if (!file_exists($located)) {
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
        return;
    }
    // Allow 3rd party plugin filter template file from their plugin
    $located = apply_filters('psc_get_template', $located, $template_name, $args, $template_path, $default_path);
    do_action('psc_before_template_part', $template_name, $template_path, $located, $args);
    include( $located );
    do_action('psc_after_template_part', $template_name, $template_path, $located, $args);
}

function psc_locate_template($template_name, $template_path = '', $default_path = '') {
    if (!$template_path) {
        $template_path = template_path();
    }
    if (!$default_path) {
        $default_path = PSC_PLUGIN_DIR_PATH . '/templates/';
    }
    // Look within passed path within the theme - this is priority
    $template = locate_template(
            array(
                trailingslashit($template_path) . $template_name,
                $template_name
            )
    );
    // Get default template
    if (!$template) {
        $template = $default_path . $template_name;
    }
    // Return what we found
    return apply_filters('psc_locate_template', $template, $template_name, $template_path);
}