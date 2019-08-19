<?php

  namespace AcfBetterSearch\Settings;

  class Save
  {
    public function __construct()
    {
      $this->initSaving();
    }

    /* ---
      Functions
    --- */

    private function initSaving()
    {
      if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'acfbs-save')) return;
      $this->saveFieldsTypes();
      $this->saveFeatures();
    }

    private function saveFieldsTypes()
    {
      if (!isset($_POST['acfbs_save']) || get_option('acfbs_lite_mode', false)) return;

      $value = $_POST['acfbs_fields_types'] ? $_POST['acfbs_fields_types'] : [];
      $types = apply_filters('acfbs_options_fields', []);

      $value = array_filter($value, function($type) use ($types) {
        return array_key_exists($type, $types);
      });
      $this->saveOption('acfbs_fields_types', $value);
    }

    private function saveFeatures()
    {
      if (!isset($_POST['acfbs_save'])) return;

      $features = apply_filters('acfbs_options_features', []);
      foreach ($features as $key => $label) {
        $value = (isset($_POST['acfbs_features']) && in_array($key, $_POST['acfbs_features']));
        $this->saveOption(sprintf('acfbs_%s', $key), $value);
      }
    }

    private function saveOption($key, $value)
    {
      if (get_option($key, false) !== false) update_option($key, $value);
      else add_option($key, $value);
    }
  }