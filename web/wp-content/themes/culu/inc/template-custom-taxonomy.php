<?php
/**
  * Function partial for custom taxonomies created with CPT UI
  *
  * @package culu
  *
  *
  */

  function cptui_register_my_staff() {
  
    /**
     * Taxonomy: Discipline Support Teams.
     */
  
    $labels = [
      "name" => __( "Discipline Support Teams", "culu" ),
      "singular_name" => __( "Discipline Support Team", "culu" ),
    ];
  
    $args = [
      "label" => __( "Discipline Support Teams", "culu" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => false,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'teams', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "teams",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "teams", [ "staff" ], $args );
  
    /**
     * Taxonomy: Areas of Expertise.
     */
  
    $labels = [
      "name" => __( "Areas of Expertise", "culu" ),
      "singular_name" => __( "Areas of Expertise", "culu" ),
    ];
  
    $args = [
      "label" => __( "Areas of Expertise", "culu" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => false,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'expertise', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "expertise",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "expertise", [ "staff" ], $args );
  
    /**
     * Taxonomy: Departments.
     */
  
    $labels = [
      "name" => __( "Departments", "culu" ),
      "singular_name" => __( "Department", "culu" ),
    ];
  
    $args = [
      "label" => __( "Departments", "culu" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => false,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'department', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "department",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "department", [ "staff" ], $args );
  
    /**
     * Taxonomy: Liaison areas.
     */
  
    $labels = [
      "name" => __( "Liaison areas", "culu" ),
      "singular_name" => __( "Liaison area", "culu" ),
    ];
  
    $args = [
      "label" => __( "Liaison areas", "culu" ),
      "labels" => $labels,
      "public" => true,
      "publicly_queryable" => true,
      "hierarchical" => false,
      "show_ui" => true,
      "show_in_menu" => true,
      "show_in_nav_menus" => true,
      "query_var" => true,
      "rewrite" => [ 'slug' => 'liaisons', 'with_front' => true, ],
      "show_admin_column" => false,
      "show_in_rest" => true,
      "rest_base" => "liaisons",
      "rest_controller_class" => "WP_REST_Terms_Controller",
      "show_in_quick_edit" => false,
      ];
    register_taxonomy( "liaisons", [ "staff" ], $args );
  }
  add_action( 'init', 'cptui_register_my_staff' );