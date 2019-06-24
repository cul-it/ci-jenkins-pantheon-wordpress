<?php

  namespace AcfBetterSearch\Settings;

  class Save
  {
    public function __construct()
    {
      $this->saveFieldsTypes();
      $this->saveFeatures();
    }

    /* ---
      Functions
    --- */

    private function saveFieldsTypes()
    {
      if (!isset($_POST['acfbs_save']) || get_option('acfbs_lite_mode', false)) return;

      $value = $_POST['acfbs_fields_types'] ? $_POST['acfbs_fields_types'] : [];
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