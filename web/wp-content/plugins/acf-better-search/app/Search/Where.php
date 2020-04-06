<?php

  namespace AcfBetterSearch\Search;

  class Where
  {
    private static $config, $wpdb;

    public function __construct()
    {
      add_filter('posts_search', ['AcfBetterSearch\Search\Where', 'sqlWhere'], 0, 2); 
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

    public static function sqlWhere($where, $query)
    {
      if (!isset($query->query_vars['s']) || empty($query->query_vars['s'])
        || !apply_filters('acfbs_search_is_available', true, $query)) return $where;

      self::loadSettings();

      $list   = [];
      $list[] = self::getACFConditions($query->query_vars['s']);
      $list[] = self::getDefaultWordPressConditions($query->query_vars['s']);

      if (in_array('file', self::$config['fields_types'])) {
        $list[] = self::getFileConditions($query->query_vars['s']);
      }

      $where = ' AND (' . implode(' OR ', array_filter($list)) . ') ';
      return $where;
    }

    private static function getACFConditions($words)
    {
      if (!self::$config['fields_types'] && !self::$config['lite_mode']) return '(1 = 2)';

      $words = !self::$config['whole_phrases'] ? explode(' ', $words) : [$words];
      $list  = [];

      foreach ($words as $word) {
        $word = addslashes($word);

        if (self::$config['whole_words']) $list[] = 'a.meta_value REGEXP \'[[:<:]]' . $word . '[[:>:]]\'';
        else $list[] = 'a.meta_value LIKE \'%' . $word . '%\'';
      }

      return sprintf('((b.meta_id IS NOT NULL) %s AND (%s))',
        (!self::$config['lite_mode']) ? 'AND (c.ID IS NOT NULL)' : '',
        implode(') AND (', $list));
    }

    private static function getDefaultWordPressConditions($words)
    {
      $words   = !self::$config['whole_phrases'] ? explode(' ', $words) : [$words];
      $columns = apply_filters('acfbs_search_post_object_fields', ['post_title', 'post_content', 'post_excerpt']);
      if (!$columns) return '';

      $list = [];
      foreach ($words as $word) {
        $word       = addslashes($word);
        $conditions = [];

        foreach ($columns as $column) {
          $conditions[] = sprintf(
            (self::$config['whole_words']) ? '(%s.%s REGEXP %s)' : '(%s.%s LIKE %s)',
            self::$wpdb->posts,
            $column,
            (self::$config['whole_words']) ? ('\'[[:<:]]' . $word . '[[:>:]]\'') : ('\'%' . $word . '%\'')
          );
        }

        $list[] = '(' . implode(' OR ', $conditions) . ')';
      }

      if (count($list) > 1) $list = '(' . implode(' AND ', $list) . ')';
      else $list = $list[0];

      return $list;
    }

    private static function getFileConditions($words)
    {
      $words = !self::$config['whole_phrases'] ? explode(' ', $words) : [$words];
      $list  = [];

      foreach ($words as $word) {
        $word   = addslashes($word);
        $list[] = 'd.post_title LIKE \'%' . $word . '%\'';

        if (self::$config['whole_words']) $list[] = 'd.post_title REGEXP \'[[:<:]]' . $word . '[[:>:]]\'';
        else $list[] = 'd.post_title LIKE \'%' . $word . '%\'';
      }

      $list = '(' . implode(') AND (', $list) . ')';
      return $list;
    }
  }