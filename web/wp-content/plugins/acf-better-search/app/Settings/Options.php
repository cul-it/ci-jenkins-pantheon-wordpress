<?php

  namespace AcfBetterSearch\Settings;

  class Options
  {
    public function __construct()
    {
      add_filter('acfbs_options_fields',   [$this, 'getFieldsSettings']);
      add_filter('acfbs_options_features', [$this, 'getFeaturesSettings'], 10, 2);
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

    public function getFeaturesSettings($value, $type = 'default')
    {
      switch ($type) {
        case 'default':
          return [
            'whole_phrases' => __('Search for whole phrases instead of each single word of phrase', 'acf-better-search'),
            'whole_words' => sprintf(
              __('Search for whole words instead of fragments within longer words %s(slower search)%s', 'acf-better-search'),
              '<em>',
              '</em>'
            ),
            'lite_mode'=> sprintf(
              __('Use %s"Lite Mode"%s - does not check field types %s(faster search, but less accurate)%s', 'acf-better-search'),
              '<strong>',
              '</strong>',
              '<em>',
              '</em>'
            ),
          ];
          break;
        case 'advanced':
          return [
            'selected_mode' => sprintf(
              __('Use %s"Selected Mode"%s - use only selected fields for searching %s(edit group of ACF fields and check option for selected fields; it does not work in "Lite Mode")%s', 'acf-better-search'),
              '<strong>',
              '</strong>',
              '<em>',
              '</em>'
            ),
            'incorrect_mode' => sprintf(
              __('Use %s"Incorrect Mode"%s - supports incorrect data structure in %s_postmeta%s table %s(slower search, but improving search among others for imported and duplicated posts)%s', 'acf-better-search'),
              '<strong>',
              '</strong>',
              '<em>',
              '</em>',
              '<em>',
              '</em>'
            ),
          ];
          break;
      }

      return $value;
    }
  }