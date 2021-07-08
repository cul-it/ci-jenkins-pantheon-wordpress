<?php

  namespace AcfBetterSearch\Settings;

  class Config
  {
    private $config;

    public function __construct()
    {
      add_filter('acfbs_config', [$this, 'getConfig'], 10, 2);
    }

    /* ---
      Functions
    --- */

    public function getConfig($value, $isForce = false)
    {
      if ($this->config && !$isForce) return $this->config;

      $types  = get_option('acfbs_fields_types', ['text', 'textarea', 'wysiwyg']);
      $config = array_merge([
        'fields_types' => $types ? $types : [],
      ], $this->getFeaturesConfig());

      $this->config = $config;
      return $config;
    }

    private function getFeaturesConfig()
    {
      $features = array_merge(apply_filters('acfbs_options_features', [], 'default'),
        apply_filters('acfbs_options_features', [], 'advanced'));

      $list = [];
      foreach ($features as $key => $label) {
        $value      = get_option(sprintf('acfbs_%s', $key), false) ? true : false;
        $list[$key] = $value;
      }
      return $list;
    }
  }