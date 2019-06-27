<?php

  namespace AcfBetterSearch\Search;

  class Request
  {
    public function __construct()
    {
      add_filter('posts_request', [$this, 'sqlDistinct'], 10, 2); 
    }

    /* ---
      Functions
    --- */

    public function sqlDistinct($sql, $query)
    {
      if (empty($query->query_vars['s'])) return $sql;

      $sql = preg_replace('/SELECT/', 'SELECT DISTINCT', $sql, 1);
      return $sql;
    }
  }