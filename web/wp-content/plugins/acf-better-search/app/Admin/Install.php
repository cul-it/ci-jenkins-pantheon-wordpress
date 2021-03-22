<?php

  namespace AcfBetterSearch\Admin;

  class Install
  {
    public function __construct()
    {
      register_activation_hook(ACFBS_FILE, [$this, 'addDefaultOptions']);
    }

    /* ---
      Functions
    --- */

    public function addDefaultOptions()
    {
      if (get_option('acfbs_notice_hidden', false) === false) {
        add_option('acfbs_notice_hidden', strtotime('+ 1 week'));
      }
    }
  }