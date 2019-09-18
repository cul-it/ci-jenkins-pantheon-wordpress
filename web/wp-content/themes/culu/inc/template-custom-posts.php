<?php
/**
  * Function partial which creates custom post types created with CPT UI
  *
  * @package culu
  *
  *
  */

 function cptui_register_my_cpts() {

 /**
  * Post Type: Staff Profiles.
  */

 $labels = array(
   "name" => __( "Staff Profiles", "custom-post-type-ui" ),
   "singular_name" => __( "Staff Profile", "custom-post-type-ui" ),
 );

 $args = array(
   "label" => __( "Staff Profiles", "custom-post-type-ui" ),
   "labels" => $labels,
   "description" => "",
   "public" => true,
   "publicly_queryable" => true,
   "show_ui" => true,
   "delete_with_user" => false,
   "show_in_rest" => true,
   "rest_base" => "",
   "rest_controller_class" => "WP_REST_Posts_Controller",
   "has_archive" => false,
   "show_in_menu" => true,
   "show_in_nav_menus" => true,
   "exclude_from_search" => false,
   "capability_type" => "post",
   "map_meta_cap" => true,
   "hierarchical" => false,
   "rewrite" => array( "slug" => "staff", "with_front" => true ),
   "query_var" => true,
   "supports" => array( "title", "editor", "thumbnail" ),
   "taxonomies" => array( "category", "post_tag" ),
 );

 register_post_type( "staff", $args );
 }

 /**
  * Post Type: Highlights.
  */

 $labels = array(
   "name" => __( "Highlights", "custom-post-type-ui" ),
   "singular_name" => __( "Highlight", "custom-post-type-ui" ),
 );

 $args = array(
   "label" => __( "Highlights", "custom-post-type-ui" ),
   "labels" => $labels,
   "description" => "",
   "public" => true,
   "publicly_queryable" => true,
   "show_ui" => true,
   "delete_with_user" => false,
   "show_in_rest" => true,
   "rest_base" => "",
   "rest_controller_class" => "WP_REST_Posts_Controller",
   "has_archive" => false,
   "show_in_menu" => true,
   "show_in_nav_menus" => true,
   "exclude_from_search" => false,
   "capability_type" => "post",
   "map_meta_cap" => true,
   "hierarchical" => false,
   "rewrite" => array( "slug" => "highlights", "with_front" => true ),
   "query_var" => true,
   "supports" => array( "title" ),
 );

 register_post_type( "highlights", $args );

 add_action( 'init', 'cptui_register_my_cpts' );
