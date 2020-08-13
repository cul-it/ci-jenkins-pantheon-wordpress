<?php

  namespace AcfBetterSearch\Search;

  class Join
  {
    private static $config, $wpdb;

    public function __construct()
    {
      add_filter('posts_join', ['AcfBetterSearch\Search\Join', 'sqlJoin'], 10, 2);
    }

    /* ---
      Functions
    --- */

    private static function loadSettings()
    {
      if (self::$wpdb && self::$config) return;

      global $wpdb;
      self::$wpdb   = $wpdb;
      self::$config = apply_filters('acfbs_config', []);
    }

    public static function sqlJoin($join, $query)
    {
      if (!isset($query->query_vars['s']) || empty($query->query_vars['s'])
        || !apply_filters('acfbs_search_is_available', true, $query)) return $join;

      self::loadSettings();
      if (!self::$config['lite_mode'] && !self::$config['fields_types']) return $join;

      $parts   = [];
      $parts[] = sprintf(
        'INNER JOIN %s AS a ON (a.post_id = %s.ID)',
        self::$wpdb->postmeta,
        self::$wpdb->posts
      );
      $parts[] = sprintf(
        'LEFT JOIN %s AS b ON (%s)',
        self::$wpdb->postmeta,
        self::getPostmetaConditions()
      );

      if ($conditions = self::getFieldsConditions()) {
        $parts[] = sprintf(
          'LEFT JOIN %s AS c ON %s',
          self::$wpdb->posts,
          $conditions
        );
      }

      if (self::checkFileFieldConditions()) {
        $parts[] = sprintf(
          'LEFT JOIN %s AS d ON (d.ID = a.meta_value)',
          self::$wpdb->posts
        );
      }

      $join .= ' ' . implode(' ', $parts) . ' ';
      return $join;
    }

    private static function getPostmetaConditions()
    {
      $list = [];

      if (self::$config['incorrect_mode']) $list[] = '(b.post_id = a.post_id)';
      else $list[] = '(b.meta_id = a.meta_id + @@auto_increment_increment)';

      $list[] = '(b.meta_key LIKE CONCAT(\'\_\', a.meta_key))';

      return '(' . implode(') AND (', $list) . ')';
    }

    private static function getFieldsConditions()
    {
      if (self::$config['lite_mode']) return null;

      $list   = [];
      $list[] = '(c.post_name = b.meta_value)';
      $list[] = '(c.post_type = \'acf-field\')';
      $list[] = self::getFieldsTypes();

      return '(' . implode(' AND ', $list) . ')';
    }

    private static function getFieldsTypes()
    {
      if (self::$config['selected_mode']) return '(c.post_content LIKE \'%s:18:"acfbs_allow_search";i:1;%\')';

      $list = [];
      foreach (self::$config['fields_types'] as $type) {
        $list[] = '(c.post_content LIKE \'%:"' . $type . '"%\')';
      }

      return '(' . implode(' OR ', $list) . ')';
    }

    private static function checkFileFieldConditions()
    {
      if (self::$config['lite_mode'] || self::$config['selected_mode']
        || !in_array('file', self::$config['fields_types'])) return false;

      return true;
    }
  }