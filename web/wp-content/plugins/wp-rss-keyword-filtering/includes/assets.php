<?php

add_action('admin_enqueue_scripts', function () {
    wp_register_style('wprss-kf-options-css', WPRSS_KF_URI . 'css/options.css', ['dashicons']);
});
