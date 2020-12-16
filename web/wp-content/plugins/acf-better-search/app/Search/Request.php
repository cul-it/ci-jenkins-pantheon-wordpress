<?php

  namespace AcfBetterSearch\Search;

  class Request
  {
    public function __construct()
    {
      add_filter('posts_request', ['AcfBetterSearch\Search\Request', 'sqlDistinct'], 10, 2); 
    }

    /* ---
      Functions
    --- */

    public static function sqlDistinct($sql, $query)
    {
      if (!isset($query->query_vars['s']) || empty($query->query_vars['s'])
        || !apply_filters('acfbs_search_is_available', true, $query)) return $sql;

      $sql = preg_replace('/SELECT/', 'SELECT DISTINCT', $sql, 1);
      return $sql;
    }
  }