<?php

  namespace AcfBetterSearch\Search;

  class Init
  {
    public function __construct()
    {
      add_action('init', [$this, 'initSearch']);
    }

    /* ---
      Functions
    --- */

    public function initSearch()
    {
      if (!$this->isSearchAvailable()) return;

      new Join();
      new Query();
      new Request();
      new Where();
    }

    private function isSearchAvailable()
    {
      $isAjax      = (defined('DOING_AJAX') && DOING_AJAX);
      $isMediaAjax = (isset($_POST['action']) && in_array($_POST['action'], ['query-attachments']));

      $status = (!$isAjax || !$isMediaAjax);
      $status = apply_filters('acfbs_is_available', $status);
      return $status;
    }
  }