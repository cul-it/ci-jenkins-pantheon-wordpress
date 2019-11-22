<?php

  namespace AcfBetterSearch\Settings;

  class Acf
  {
    private $config;

    public function __construct()
    {
      add_filter('acf/render_field_settings', [$this, 'addFieldSettings']);
    }

    /* ---
      Functions
    --- */

    public function addFieldSettings($field)
    {
      $config = apply_filters('acfbs_config', []);
      if (!$config['selected_mode'] || !in_array($field['type'], $config['fields_types'])) return;

      acf_render_field_setting($field, [
        'label'        => __('Allow use for search?', 'acf-better-search'),
        'instructions' => sprintf(
          __('Only values from fields with selected this option will be used by %sACF: Better Search%s plugin.', 'acf-better-search'),
          '<strong>',
          '</strong>'
        ),
        'name'         => 'acfbs_allow_search',
        'type'         => 'true_false',
        'ui'           => 1,
      ], true);
    }
  }