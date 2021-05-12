<?php

add_filter( 'default_args', 'wprss_c_shortcode_add_category_arg' );
/**
 * Sets the default arguments for category fields.
 *
 * @since 1.0
 * @return array The default arguments for new categories.
 */
function wprss_c_shortcode_add_category_arg() {
    $args = array(
        'feed_name' => NULL,
        'category'  => ''
    );
    return $args;
}


//add_action( 'init', 'wprss_c_check_existing_feeds' );
/**
 * Checks existing feed sources, and sets the default category to
 * feed sources that do not have it set
 *
 * @since 1.0
 */
function wprss_c_check_existing_feeds(){
    $option = get_option( 'wprss_c_check_existing_feeds', '1' );
    if ( $option === '0' )
        return;
    else update_option( 'wprss_c_check_existing_feeds', '0' );

    // Get all current wprss_feed posts
    $wprss_feed_sources = $pages = get_posts(
        array(
            'post_type' => 'wprss_feed',
            'posts_per_page'=> -1,
            'post_status' => array(
                'publish',
                'pending',
                'draft',
                'auto-draft',
                'future',
                'private',
                'inherit',
                'trash'
            )
        )
    );

    // For each posts, check if they have a category set, otherwise give the default
    foreach ( $wprss_feed_sources as $wprss_feed_source ) {
        $terms = wp_get_post_terms( $wprss_feed_source->ID, 'wprss_category' );
        // If the post has no categories, give it the default category
        if ( $terms === null || empty( $terms ) || count( $terms ) === 0 ) {
            wp_set_object_terms( $wprss_feed_source->ID, 'uncategorized', 'wprss_category' );
        }
    }
}


add_action( 'wprss_opml_inserted_feed', 'wprss_cat_import_opml_category', 10, 2 );
/**
 * Action called after a feed source is inserted into the DB via the OPML importer.
 * The function will check the OPML <outline> element for a category attribute, and
 * add the necessary categories to the feed source.
 *
 * @since 1.2.7
 * @param int $inserted_id The ID of the inserted feed source
 * @param array $outline An associative array containing the data of the outline element.
 */
function wprss_cat_import_opml_category( $inserted_id, $outline ) {
    // Check if the outline element has categories
    if ( isset( $outline['categories'] ) && is_array( $outline['categories'] ) ) {
        // Get the categories
        $categories = $outline['categories'];

        if ( count( $categories ) > 0 ) {
            $i = 0;
            $term_ids = array();
            // Iterate through each part
            foreach ( $categories as $category ) {
                // If category does not exist, create it
                if ( !term_exists( $category, 'wprss_category' ) ) {
                    // Function returns an array with term ID and tax ID
                    $a = wp_insert_term( $category, 'wprss_category' );
                }
                // Otherwise, get the existing ID by name
                else {
                    // function returns an array with term ID and tax ID
                    $a = term_exists( $category, 'wprss_category' );
                }
                // Get the term ID
                $term_ids[] = intval( $a['term_id'] );
            }
            // Add the categories to the feed source
            // Last argument is TRUE: add terms, FALSE: override
            // On first time, we override to remove the Uncategorized category.
            wp_set_post_terms( $inserted_id, $term_ids, 'wprss_category', $i > 0 );
            $i++;
        }

    }
}
