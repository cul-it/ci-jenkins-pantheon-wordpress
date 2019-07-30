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
      if (empty($query->query_vars['s'])) return $join;
      $this->loadSettings();
      if (!$this->config['fields_types'] && !$this->config['lite_mode']) return $join;

      $parts   = [];
      $parts[] = sprintf(
        'INNER JOIN %s AS a ON (a.post_id = %s.ID)',
        $this->wpdb->postmeta,
        $this->wpdb->posts
      );
      $parts[] = sprintf(
        'INNER JOIN %s AS b ON ((b.meta_id = a.meta_id + @@auto_increment_increment) AND (b.post_id = %s.ID))',
        $this->wpdb->postmeta,
        $this->wpdb->posts
      );

      if (!$this->config['lite_mode']) {
        $parts[] = sprintf(
          'INNER JOIN %s AS c ON %s',
          $this->wpdb->posts,
          $this->getFieldsTypesConditions($this->config['selected_mode'])
        );

        if (!$this->config['selected_mode'] && in_array('file', $this->config['fields_types'])) {
          $parts[] = sprintf(
            'LEFT JOIN %s AS d ON (d.ID = a.meta_value)',
            $this->wpdb->posts
          );
        }
      }

      $join .= ' ' . implode(' ', $parts);
      return $join;
    }

    private function getFieldsTypesConditions($isSelectedMode)
    {
      $types = [];
      $list  = [];

      if (!$isSelectedMode) {
        foreach ($this->config['fields_types'] as $type) {
          $types[] = '(c.post_content LIKE \'%:"' . $type . '"%\')';
        }
      } else {
        $types[] = '(c.post_content LIKE \'%s:18:"acfbs_allow_search";i:1;%\')';
      }

      $list[] = '(c.post_type = \'acf-field\')';

      if (count($types) > 1) {
        $list[] = '(' . implode(' OR ', $types) . ')';
      } else {
        $list[] = $types[0];
      }
      
      $list = '(' . implode(' AND ', $list) . ')';
      return $list;
    }
  }