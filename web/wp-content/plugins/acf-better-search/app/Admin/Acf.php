<?php

  namespace AcfBetterSearch\Admin;

  class Acf
  {
    public function __construct()
    {
      add_action('admin_notices', [$this, 'showAdminError']);
    }

    /* ---
      Functions
    --- */

    public function showAdminError()
    {
      if (function_exists('acf_get_setting')) return;

      require_once ACFBS_PATH . 'resources/components/notices/acf.php';
    }
  }