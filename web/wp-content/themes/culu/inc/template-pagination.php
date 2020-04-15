<?php
/**
 * Custom template for pagination for this theme
 *
 *
 * @package culu
 */



function culu_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}


add_action('init', 'culu_pagination'); // Add our HTML5 Pagination
