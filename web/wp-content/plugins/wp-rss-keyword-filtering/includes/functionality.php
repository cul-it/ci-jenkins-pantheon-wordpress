<?php

add_filter( 'wprss_insert_post_item_conditionals', 'wprss_kf_check_post_item_keywords', 10, 3 );
/**
 * Checks the given item for the presence of any keywords set for the feed source
 * with ID $feed_ID. At least 1 keyword mst be found for the check to be successful.
 *
 * @return The item if at least 1 keywrod is found. NULL otherwise.
 * @param item      The item to be checked for keywords
 * @param feed_ID   The ID of the item's feed source
 * @since 1.0
 */
function wprss_kf_check_post_item_keywords( $item, $feed_ID, $permalink ) {
    // If the item is NULL (i.e. already flagged as not being inserted into the DB) then return NULL.
    if ( $item === NULL ) return NULL;

    // Retrieve the keywords stored in the global settings
    $settings = get_option( 'wprss_settings_kf', array() );
    $settings = wp_parse_args( $settings, wprss_kf_default_options() );

    $filter_title = get_post_meta( $feed_ID, 'wprss_filter_title', TRUE );
    if ( $filter_title === '' ) $filter_title = 'true';

    $filter_content = get_post_meta( $feed_ID, 'wprss_filter_content', TRUE );
    if ( $filter_content === '' ) $filter_content = 'true';

    // Prepare the filtering options
    $filtering_opts = array();
    if ( $filter_title == 'true' ) $filtering_opts[] = 'title';
    if ( $filter_content == 'true' ) $filtering_opts[] = 'content';

    $settings_keywords = $settings['keywords'];
    $settings_keywords_any = $settings['keywords_any'];
    $settings_keywords_not = $settings['keywords_not'];
    $settings_keywords_tags = $settings['keywords_tags'];
    $settings_keywords_not_tags = $settings['keywords_not_tags'];

    // Retrieve the feed source's keywords meta data
    $post_keywords = get_post_meta( $feed_ID, 'wprss_keywords', true );
    $post_keywords_any = get_post_meta( $feed_ID, 'wprss_keywords_any', true );
    $post_keywords_not = get_post_meta( $feed_ID, 'wprss_keywords_not', true );
    $post_keywords_tags = get_post_meta( $feed_ID, 'wprss_keywords_tags', true );
    $post_keywords_not_tags = get_post_meta( $feed_ID, 'wprss_keywords_not_tags', true );


    //=== KEYWORDS ===========================================================
    //=== All keywords must be matched for the feed item to be imported ======

    // Generate an array, that explodes the comma separated keywords and trims each array entry
    // from leading / trailing whitespace.
    $settingsKeywordsArray = array_filter( array_map( 'trim', explode( ',', $settings_keywords ) ) );
    $postKeywordsArray = array_filter( array_map( 'trim', explode( ',', $post_keywords ) ) );
    $keywords = array_merge( $settingsKeywordsArray, $postKeywordsArray );

    if ( count( $keywords ) > 0 ) {
        // Set match to TRUE
        $match = TRUE;
        // For each keyword ...
        foreach ( $keywords as $keyword ) {
            // If the item obeys the filtering
            if ( wprss_kf_filter_item( $item, $keyword, $filtering_opts ) === FALSE ) {
                // Set match to false and stop any further checking
                $match = FALSE;
                break;
            }
        }
    } else $match = TRUE;

    if ( $match === FALSE ) return NULL;


    //=== ANY KEYWORDS =============================================================
    //=== If at least one of the keywords is found, the feed item is imported ======


    // Generate an array, that explodes the comma separated keywords and trims each array entry
    // from leading / trailing whitespace.
    $settingsKeywordsArray = array_filter( array_map( 'trim', explode( ',', $settings_keywords_any ) ) );
    $postKeywordsArray = array_filter( array_map( 'trim', explode( ',', $post_keywords_any ) ) );
    $keywords = array_merge( $settingsKeywordsArray, $postKeywordsArray );

    if ( count( $keywords ) > 0 ) {
        // Set match to FALSE
        $match = FALSE;
        // For each keyword ...
        foreach ( $keywords as $keyword ) {
            // If the item obeys the filtering
            if ( wprss_kf_filter_item( $item, $keyword, $filtering_opts ) === TRUE ) {
                // Set match to true and stop any further checking
                $match = TRUE;
                break;
            }
            // If matched, no point to continue checking. Break.
            if ( $match === TRUE ) break;
        }
    } else $match = TRUE;

    if ( $match === FALSE ) return NULL;


    //=== NOT KEYWORDS =================================================================
    //=== If at least one of the keywords is found, the feed item is not imported ======


    // Generate an array, that explodes the comma separated keywords and trims each array entry
    // from leading / trailing whitespace.
    $settingsKeywordsArray = array_filter( array_map( 'trim', explode( ',', $settings_keywords_not ) ) );
    $postKeywordsArray = array_filter( array_map( 'trim', explode( ',', $post_keywords_not ) ) );
    $keywords = array_merge( $settingsKeywordsArray, $postKeywordsArray );

    if ( count( $keywords ) > 0 ) {
        // Set match to TRUE
        $match = TRUE;
        // For each keyword ...
        foreach ( $keywords as $keyword ) {
            // If the item does not obeys the filtering (keyword found)
            if ( wprss_kf_filter_item( $item, $keyword, $filtering_opts ) === TRUE ) {
                // Set match to false and stop any further checking
                $match = FALSE;
                break;
            }
        }
    }
    else $match = TRUE;

    if ( $match === FALSE ) return NULL;


    //=== TAGS ====================================================================
    //=== If at least one of the tags is found, the feed item is imported =========


    // Generate an array, that explodes the comma separated tags and trims each array entry
    // from leading / trailing whitespace.
    $settingsTagsArray = array_filter( array_map( 'trim', explode( ',', $settings_keywords_tags ) ) );
    $postTagsArray = array_filter( array_map( 'trim', explode( ',', $post_keywords_tags ) ) );
    $tags = array_merge( $settingsTagsArray, $postTagsArray );

    if ( count( $tags ) > 0 ) {
        // Set match to FALSE
        $match = FALSE;
        // For each tag ...
        foreach ( $tags as $tag ) {
            // Get the post tags
            $itemTagObjects = $item->get_categories();
            $itemTags = array_filter( array_map( 'wprss_kf_get_tag_label', $itemTagObjects ) );
            // If the tag is found
            $tagFound = in_array( WPRSS_MBString::mb_strtolower($tag), $itemTags );

            // If the tag is found, set match to true and stop checking
            if ( $tagFound === TRUE ) {
                $match = TRUE;
                break;
            }
        }
    }
    else $match = TRUE;

    if ( $match === FALSE ) return NULL;


    //=== NOT TAGS ====================================================================
    //=== If at least one of the tags is found, the feed item is NOT imported =========

    // Generate an array, that explodes the comma separated tags and trims each array entry
    // from leading / trailing whitespace.
    $settingsNotTagsArray = array_filter( array_map( 'trim', explode( ',', $settings_keywords_not_tags ) ) );
    $postNotTagsArray = array_filter( array_map( 'trim', explode( ',', $post_keywords_not_tags ) ) );
    $notTags = array_merge( $settingsNotTagsArray, $postNotTagsArray );

    if ( count( $notTags ) > 0 ) {
        // Set match to TRUE
        $match = TRUE;
        // Get the post tags
        $itemTagObjects = $item->get_categories();
        // For each tag ...
        foreach ( $notTags as $tag ) {
            // Filter the item categories for just the labels
            $itemTags = array_filter( array_map( 'wprss_kf_get_tag_label', $itemTagObjects ) );
            // If the tag is found
            $tagFound = in_array( WPRSS_MBString::mb_strtolower($tag), $itemTags );

            // If the tag is found, set match to false and stop checking
            if ( $tagFound === TRUE ) {
                $match = FALSE;
                break;
            }
        }
    }
    else $match = TRUE;

    if ( $match === FALSE ) return NULL;


    // If the item passed through all the filters, then return it to be imported.
    return $item;
}


/**
 * Returns the label of the given tag. Made to be used in array_map() calls.
 *
 * @param $tag The SimplePie tag object
 * @return string The label of the tag
 * @since 1.3
 */
function wprss_kf_get_tag_label( $tag ) {
    return WPRSS_MBString::mb_strtolower( $tag->get_label() );
}


/**
 * Checks if the given feed item contains the keyword in any of the given filtering properties.
 *
 * @param $item         The feed item
 * @param $keyword      The keyword
 * @param $filtering    The filtering properties to use. If none are specified, the function will return TRUE, as though filtering was ignored.
 * @return boolean      True if the item contains the keyword in any of the filtering properties. False otherwise.
 * @since 1.4
 */
function wprss_kf_filter_item( $item, $keyword, $filtering = array() ) {
    if ( count( $filtering ) === 0 ) return TRUE;

    // For each filtering property
    foreach ( $filtering as $prop ) {
        switch( strtolower( $prop ) ) {
            case 'title':
                $found = !( WPRSS_MBString::mb_stripos( $item->get_title(), $keyword ) === FALSE );
                if ( $found === TRUE ) return TRUE;
                break;
            case 'content':
                $found = !( WPRSS_MBString::mb_stripos( $item->get_content(), $keyword ) === FALSE );
                if ( $found === TRUE ) return TRUE;
                break;
        }
    }

    return FALSE;
}


add_filter( 'wprss_process_shortcode_args', 'wprss_kf_add_shortcode_filtering', 10, 2 );
/**
 * Adds the 'filter' argument to the shortcode, so that it is passed to the query args.
 *
 * @since 1.4
 */
function wprss_kf_add_shortcode_filtering( $query_args, $args ) {
    if ( isset( $args['filter'] ) ) {
        $query_args['filter'] = $args['filter'];
    }
    return $query_args;
}

add_filter( 'wprss_display_feed_items_query', 'wprss_kf_add_filtering_search_parameter', 10, 2 );
/**
 * Turns the filter shortcode argument into the WordPress search parameter.
 *
 * @since 1.4
 */
function wprss_kf_add_filtering_search_parameter( $feed_item_args, $query_args ) {
    if ( isset( $query_args['filter'] ) ) {
        $feed_item_args['s'] = $query_args['filter'];
    }
    return $feed_item_args;
}
