<?php

  namespace AcfBetterSearch\Settings;

  class Config
  {
    private $config;

    public function __construct()
    {
      add_filter('acfbs_config', [$this, 'getConfig']);
    }

    /* ---
      Functions
    --- */

    public function getConfig()
    {
      if ($this->config) return $this->config;

      $value  = get_option('acfbs_fields_types', ['text', 'textarea', 'wysiwyg']);
      $config = array_merge([
        'fields_types' => $value ? $value : [],
      ], $this->getFeaturesConfig());

      $this->config = $config;
      return $config;
    }

    private function getFeaturesConfig()
    {
      $features = apply_filters('acfbs_options_features', []);

      $list = [];
      foreach ($features as $key => $label) {
        $value      = get_option(sprintf('acfbs_%s', $key), false) ? true : false;
        $list[$key] = $value;
      }
      return $list;
    }
  }