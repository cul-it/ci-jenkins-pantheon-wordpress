<?php

  namespace AcfBetterSearch\Search;

  class _Core
  {
    public function __construct()
    {
      if (!$this->isSearchAvailable()) return;

      new Join();
      new Query();
      new Request();
      new Where();
    }

    /* ---
      Actions
    --- */

    private function isSearchAvailable()
    {
      $isAjax      = (defined('DOING_AJAX') && DOING_AJAX);
      $isMediaAjax = (isset($_POST['action']) && in_array($_POST['action'], ['query-attachments']));

      $status = (!$isAjax || !$isMediaAjax);
      apply_filters('acfbs_is_available', $status);
      return $status;
    }
  }