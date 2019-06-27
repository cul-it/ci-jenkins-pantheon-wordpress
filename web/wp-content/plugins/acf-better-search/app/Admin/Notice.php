<?php

  namespace AcfBetterSearch\Admin;

  class Notice
  {
    private $option = 'acfbs_notice_hidden';

    public function __construct()
    {
      add_filter('acfbs_notice_url',     [$this, 'showNoticeUrl']); 
      add_action('admin_notices',        [$this, 'showAdminNotice']);
      add_action('wp_ajax_acfbs_notice', [$this, 'hideAdminNotice']);
    }

    /* ---
      Functions
    --- */

    public function showNoticeUrl()
    {
      $url = admin_url('admin-ajax.php?action=acfbs_notice');
      return $url;
    }

    public function showAdminNotice()
    {
      if (($_SERVER['PHP_SELF'] !== '/wp-admin/index.php') ||
        (get_option($this->option, 0) >= time())) return;

      require_once ACFBS_PATH . 'resources/components/notices/thanks.php';
    }

    public function hideAdminNotice()
    {
      $isPermanent = isset($_POST['is_permanently']) && $_POST['is_permanently'];
      $expires     = strtotime($isPermanent ? '+10 years' : '+ 1 month');

      $this->saveOption($expires);
    }

    public function saveOption($value)
    {
      if (get_option($this->option, false) !== false) update_option($this->option, $value);
      else add_option($this->option, $value);
    }
  }