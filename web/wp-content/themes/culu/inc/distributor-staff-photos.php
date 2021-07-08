<?php

/**
 * Send staff photo source URL as postmeta via Distributor
 * and expose it via the WP Rest API so it can be consumed
 * by Vue app
 *
 * @package culu
 */


/**
 * Capture staff photo URL as postmeta on post save
 * -- this is critical for all unit sites
 * -- (the source of data or pushers in Distributor land)
 */
add_action('save_post_staff', 'staff_photo_url_postmeta', 10, 2);

function staff_photo_url_postmeta($post_id, $post)
{
    // Bail out if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Photo is ACF (image_aspect_ratio_crop)
    $photo = get_field('photo', $post_id);

    // Set the photo URL as postmeta
    // -- it will automatically be included by Distributor when pushed
    if ($photo) {
        update_post_meta($post_id, 'staff_photo_url', $photo['url']);
    }
}


/**
 * Register `staff_photo_url` postmeta for the WP Rest API /staff endpoint
 * -- this is critical for the central WP instance receiving all pushed content
 * -- (the external connection in Distributor land)
 */
add_action('rest_api_init', 'register_staff_photo_url');

// add_action('rest_api_init', function () {
function register_staff_photo_url()
{
    register_rest_field(
        'staff',
        'staff_photo_url',
        array(
            'get_callback'    => 'get_staff_photo_url',
            'schema'          => null,
        )
    );
}

function get_staff_photo_url($post, $key)
{
    return get_post_meta($post['id'], $key);
}
