<?php

  namespace AcfBetterSearch\Admin;

  class Uninstall
  {
    public function __construct()
    {
      register_uninstall_hook(ACFBS_FILE, ['AcfBetterSearch\Admin\Uninstall', 'removePluginSettings']);
    }

    /* ---
      Functions
    --- */

    public static function removePluginSettings()
    {
      delete_option('acfbs_fields_types');
      delete_option('acfbs_whole_phrases');
      delete_option('acfbs_whole_words');
      delete_option('acfbs_lite_mode');
      delete_option('acfbs_selected_mode');
      delete_option('acfbs_notice_hidden');
    }
  }