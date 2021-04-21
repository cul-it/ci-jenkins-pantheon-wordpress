<?php

  namespace AcfBetterSearch\Settings;

  class Options
  {
    public function __construct()
    {
      add_filter('acfbs_options_fields',   [$this, 'getFieldsSettings']);
      add_filter('acfbs_options_features', [$this, 'getFeaturesSettings'], 10, 3);
    }

    /* ---
      Functions
    --- */

    public function getFieldsSettings()
    {
      return [
        'text'     => __('Text', 'acf-better-search'),
        'textarea' => __('Text Area', 'acf-better-search'),
        'number'   => __('Number', 'acf-better-search'),
        'email'    => __('Email', 'acf-better-search'),
        'url'      => __('Url', 'acf-better-search'),
        'file'     => sprintf(__('File %s(it does not work in "Lite Mode")%s', 'acf-better-search'), '<em>', '</em>'),
        'wysiwyg'  => __('Wysiwyg Editor', 'acf-better-search'),
        'select'   => __('Select', 'acf-better-search'),
        'checkbox' => __('Checkbox', 'acf-better-search'),
        'radio'    => __('Radio Button', 'acf-better-search'),
      ];
    }

    public function getFeaturesSettings($value, $type = 'default', $config = [])
    {
      switch ($type) {
        case 'default':
          return [
            'whole_phrases' => [
              'label'     => __('Search for whole phrases instead of each single word of phrase', 'acf-better-search'),
              'is_active' => true,
            ],
            'whole_words' => [
              'label'     => sprintf(
                __('Search for whole words instead of fragments within longer words %s(slower search)%s', 'acf-better-search'),
                '<em>',
                '</em>'
              ),
              'is_active' => true,
            ],
            'regex_spencer' => [
              'label'     => sprintf(
                __('Use implementation of regular expression by Henry Spencer to search for whole words %s(for newer versions of MySQL where default does not work)%s', 'acf-better-search'),
                '<em>',
                '</em>'
              ),
              'is_active' => (isset($config['whole_words']) && $config['whole_words']),
            ],
            'lite_mode' => [
              'label'     => sprintf(
                __('Use %s"Lite Mode"%s - does not check field types %s(faster search, but less accurate)%s', 'acf-better-search'),
                '<strong>',
                '</strong>',
                '<em>',
                '</em>'
              ),
              'is_active' => true,
            ],
          ];
          break;
        case 'advanced':
          return [
            'selected_mode' => [
              'label'     => sprintf(
                __('Use %s"Selected Mode"%s - use only selected fields for searching %s(edit group of ACF fields and check option for selected fields; it does not work in "Lite Mode")%s', 'acf-better-search'),
                '<strong>',
                '</strong>',
                '<em>',
                '</em>'
              ),
              'is_active' => (!isset($config['lite_mode']) || !$config['lite_mode']),
            ],
            'incorrect_mode' => [
              'label'     => sprintf(
                __('Use %s"Incorrect Mode"%s - supports incorrect data structure in %s_postmeta%s table %s(slower search, but improving search among others for imported and duplicated posts)%s', 'acf-better-search'),
                '<strong>',
                '</strong>',
                '<em>',
                '</em>',
                '<em>',
                '</em>'
              ),
              'is_active' => true,
            ],
          ];
          break;
      }

      return $value;
    }
  }