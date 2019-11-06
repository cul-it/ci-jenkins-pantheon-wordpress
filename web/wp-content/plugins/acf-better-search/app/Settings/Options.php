<?php

  namespace AcfBetterSearch\Settings;

  class Options
  {
    public function __construct()
    {
      add_filter('acfbs_options_fields',   [$this, 'getFieldsSettings']);
      add_filter('acfbs_options_features', [$this, 'getFeaturesSettings']);
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
        'file'     => sprintf(__('File %s(it doesn\'t work in lite mode)%s', 'acf-better-search'), '<em>', '</em>'),
        'wysiwyg'  => __('Wysiwyg Editor', 'acf-better-search'),
        'select'   => __('Select', 'acf-better-search'),
        'checkbox' => __('Checkbox', 'acf-better-search'),
        'radio'    => __('Radio Button', 'acf-better-search'),
      ];
    }

    public function getFeaturesSettings()
    {
      return [
        'whole_phrases' => __('Search for whole phrases instead of each single word of phrase', 'acf-better-search'),
        'lite_mode'     => sprintf(
          __('Use lite mode %s(faster search, without checking field types)%s', 'acf-better-search'),
          '<em>',
          '</em>'
        ),
        'selected_mode' => sprintf(
          __('Use only selected fields for searching %s(edit group of ACF fields and check option for selected fields; it doesn\'t work in lite mode)%s', 'acf-better-search'),
          '<em>',
          '</em>'
        ),
      ];
    }
  }