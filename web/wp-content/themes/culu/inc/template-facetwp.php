<?php
/**
  * Fix Draw Attention and FacetWP Query issue.
  * When FacetWP and Draw Attention are both active in a unit site, 
  * FacetWP will use the da_image post type in its query instead of 
  * the one that we define, which breaks FacetWP anywhere 
  * we have it on that site.
  *
  * https://facetwp.com/documentation/troubleshooting/#wrongquery
  *
  * @package culu
  *
  */

  add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { if ( 'da_image' == $query->get( 'post_type' ) ) { $is_main_query = false; } return $is_main_query; }, 10, 2 );
