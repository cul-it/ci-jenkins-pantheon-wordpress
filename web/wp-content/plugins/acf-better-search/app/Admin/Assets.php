<?php

  namespace AcfBetterSearch\Admin;

  class Assets
  {
    public function __construct()
    {
      add_filter('admin_enqueue_scripts', [$this, 'loadStyles']);
      add_filter('admin_enqueue_scripts', [$this, 'loadScripts']);
    }

    /* ---
      Functions
    --- */

    public function loadStyles()
    {
      wp_register_style('acf-better-search', ACFBS_URL . 'public/build/css/styles.css', '', ACFBS_VERSION);
      wp_enqueue_style('acf-better-search');
    }

    public function loadScripts()
    {
      wp_register_script('acf-better-search', ACFBS_URL . 'public/build/js/scripts.js', 'jquery', ACFBS_VERSION, true);
      wp_enqueue_script('acf-better-search');
    }
  }