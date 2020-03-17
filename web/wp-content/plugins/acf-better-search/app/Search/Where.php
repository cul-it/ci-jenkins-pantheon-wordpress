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
        $word = addslashes($word);

        if ($this->config['whole_words']) $list[] = 'a.meta_value REGEXP \'[[:<:]]' . $word . '[[:>:]]\'';
        else $list[] = 'a.meta_value LIKE \'%' . $word . '%\'';
      }
      return '(' . implode(') AND (', $list) . ')';
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
            ($this->config['whole_words']) ? '(%s.%s REGEXP %s)' : '(%s.%s LIKE %s)',
            $this->wpdb->posts,
            $column,
            ($this->config['whole_words']) ? ('\'[[:<:]]' . $word . '[[:>:]]\'') : ('\'%' . $word . '%\'')
          );
        }

        $list[] = '(' . implode(' OR ', $conditions) . ')';
      }

      if (count($list) > 1) $list = '(' . implode(' AND ', $list) . ')';
      else $list = $list[0];

      return $list;
    }

    private function getFileConditions($words)
    {
      $words = !$this->config['whole_phrases'] ? explode(' ', $words) : [$words];
      $list  = [];

      foreach ($words as $word) {
        $word   = addslashes($word);
        $list[] = 'd.post_title LIKE \'%' . $word . '%\'';

        if ($this->config['whole_words']) $list[] = 'd.post_title REGEXP \'[[:<:]]' . $word . '[[:>:]]\'';
        else $list[] = 'd.post_title LIKE \'%' . $word . '%\'';
      }

      $list = '(' . implode(') AND (', $list) . ')';
      return $list;
    }
  }