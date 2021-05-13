<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package culu
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function culu_body_classes($classes)
{
    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no-sidebar';
    }

    return $classes;
}
add_filter('body_class', 'culu_body_classes');

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function culu_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'culu_pingback_header');

/**
 * Check if user enter an email via customizer
 */
function checkEmail($email)
{
    $find1 = strpos($email, '@');
    $find2 = strpos($email, '.');
    return ($find1 !== false && $find2 !== false && $find2 > $find1);
}

/**
 * Vue assets
 */
function vue_bundled_assets()
{
    // register the Vue build scripts
    wp_register_script(
        'vue-software-list',
        get_template_directory_uri() . '/vue/dist/js/software.js', // for development: 'http://localhost:8080/js/software.js'.
        array(),
        false,
        true
    );

    wp_register_script(
        'vue-staff-profiles',
        get_template_directory_uri() . '/vue/dist/js/staff.js', // for development: 'http://localhost:8080/js/staff.js'.
        array(),
        false,
        true
    );

    wp_register_script(
        'vue-building-occupancy',
        get_template_directory_uri() . '/vue/dist/js/occupancy.js', // for development: 'http://localhost:8080/js/occupancy.js'.
        array(),
        false,
        true
    );

    wp_register_script(
        'vue-vendors-chunk',
        get_template_directory_uri() . '/vue/dist/js/chunk-vendors.js', // for development: 'http://localhost:8080/js/chunk-vendors.js'.
        array(),
        false,
        true
    );
}

add_action('wp_enqueue_scripts', 'vue_bundled_assets');
