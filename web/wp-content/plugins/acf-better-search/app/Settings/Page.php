<?php

  namespace AcfBetterSearch\Settings;

  class Page
  {
    public function __construct()
    {
      add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    /* ---
      Functions
    --- */

    public function addSettingsPage()
    {
      if (is_network_admin()) return;

      add_submenu_page(
        'options-general.php',
        'ACF: Better Search',
        'ACF: Better Search',
        'manage_options',
        'acfbs_admin_page',
        [$this, 'showSettingsPage']
      );
    }

    public function showSettingsPage()
    {
      new Save();
      require_once ACFBS_PATH . 'resources/views/settings.php';
    }
  }