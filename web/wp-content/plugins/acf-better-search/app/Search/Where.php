<?php

  namespace AcfBetterSearch\Search;

  class Where
  {
    private $config, $wpdb;

    public function __construct()
    {
      add_filter('posts_search', [$this, 'sqlWhere'], 0, 2); 
    }

    /* ---
      Functions
    --- */

    private function loadSettings()
    {
      if ($this->wpdb && $this->config) return;
      global $wpdb;
      $this->wpdb   = $wpdb;
      $this->config = apply_filters('acfbs_config', []);
    }

    public function sqlWhere($where, $query)
    {
      if (!isset($query->query_vars['s']) || empty($query->query_vars['s'])
        || !apply_filters('acfbs_search_is_available', true, $query)) return $where;

      $this->loadSettings();

      $list   = [];
      $list[] = $this->getACFConditions($query->query_vars['s']);
      $list[] = $this->getDefaultWordPressConditions($query->query_vars['s']);

      if (in_array('file', $this->config['fields_types'])) {
        $list[] = $this->getFileConditions($query->query_vars['s']);
      }

      $where = ' AND (' . implode(' OR ', $list) . ') ';
      return $where;
    }

    private function getACFConditions($words)
    {
      if (!$this->config['fields_types'] && !$this->config['lite_mode']) return '(1 = 2)';

      $words = !$this->config['whole_phrases'] ? explode(' ', $words) : [$words];
      $list  = [];

      foreach ($words as $word) {
        $word   = addslashes($word);
        $list[] = 'a.meta_value LIKE \'%' . $word . '%\'';
      }

      $list = '(' . implode(') AND (', $list) . ')';

      if (!$this->config['lite_mode']) {
        $list = '((c.post_name = b.meta_value) AND ' . $list . ')';
      } else {
        $list = '((b.meta_value LIKE \'field_%\') AND ' . $list . ')';
      }

      return $list;
    }

    private function getDefaultWordPressConditions($words)
    {
      $words   = !$this->config['whole_phrases'] ? explode(' ', $words) : [$words];
      $columns = apply_filters('acfbs_search_post_object_fields', ['post_title', 'post_content', 'post_excerpt']);
      $list    = [];

      foreach ($words as $word) {
        $word       = addslashes($word);
        $conditions = [];

        foreach ($columns as $column) {
          $conditions[] = sprintf(
            '(%s.%s LIKE %s)',
            $this->wpdb->posts,
            $column,
            '\'%' . $word . '%\''
          );
        }

        $list[] = '(' . implode(' OR ', $conditions) . ')';
      }

      if (count($list) > 1) {
        $list = '(' . implode(' AND ', $list) . ')';
      } else {
        $list = $list[0];
      }

      return $list;
    }

    private function getFileConditions($words)
    {
      $words = !$this->config['whole_phrases'] ? explode(' ', $words) : [$words];
      $list  = [];

      foreach ($words as $word) {
        $word   = addslashes($word);
        $list[] = 'd.post_title LIKE \'%' . $word . '%\'';
      }

      $list = '(' . implode(') AND (', $list) . ')';
      return $list;
    }
  }