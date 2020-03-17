<?php

  namespace AcfBetterSearch\Search;

  class Join
  {
    private $config, $wpdb;

    public function __construct()
    {
      add_filter('posts_join', [$this, 'sqlJoin'], 10, 2);
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

    public function sqlJoin($join, $query)
    {
      if (!isset($query->query_vars['s']) || empty($query->query_vars['s'])
        || !apply_filters('acfbs_search_is_available', true, $query)) return $join;

      $this->loadSettings();
      if (!$this->config['lite_mode'] && !$this->config['fields_types']) return $join;

      $parts   = [];
      $parts[] = sprintf(
        'INNER JOIN %s AS a ON (a.post_id = %s.ID)',
        $this->wpdb->postmeta,
        $this->wpdb->posts
      );
      $parts[] = sprintf(
        'INNER JOIN %s AS b ON ((b.meta_id = a.meta_id + @@auto_increment_increment) AND (b.meta_key LIKE CONCAT(\'\_\', a.meta_key)))',
        $this->wpdb->postmeta,
        $this->wpdb->posts
      );

      if ($conditions = $this->getFieldsConditions()) {
        $parts[] = sprintf(
          'INNER JOIN %s AS c ON %s',
          $this->wpdb->posts,
          $conditions
        );
      }

      if ($this->checkFileFieldConditions()) {
        $parts[] = sprintf(
          'LEFT JOIN %s AS d ON (d.ID = a.meta_value)',
          $this->wpdb->posts
        );
      }

      $join .= ' ' . implode(' ', $parts) . ' ';
      return $join;
    }

    private function getFieldsConditions()
    {
      if ($this->config['lite_mode']) return null;

      $list   = [];
      $list[] = '(c.post_name = b.meta_value)';
      $list[] = '(c.post_type = \'acf-field\')';
      $list[] = $this->getFieldsTypes();

      $list = '(' . implode(' AND ', $list) . ')';
      return $list;
    }

    private function getFieldsTypes()
    {
      if ($this->config['selected_mode']) return '(c.post_content LIKE \'%s:18:"acfbs_allow_search";i:1;%\')';

      $list = [];
      foreach ($this->config['fields_types'] as $type) {
        $list[] = '(c.post_content LIKE \'%:"' . $type . '"%\')';
      }
      return '(' . implode(' OR ', $list) . ')';
    }

    private function checkFileFieldConditions()
    {
      if ($this->config['lite_mode'] || $this->config['selected_mode']
        || !in_array('file', $this->config['fields_types'])) return false;

      return true;
    }
  }