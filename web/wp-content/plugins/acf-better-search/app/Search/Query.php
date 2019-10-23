<?php

  namespace AcfBetterSearch\Search;

  class Query
  {
    public function __construct()
    {
      add_filter('pre_get_posts', [$this, 'queryArgs']); 
    }

    /* ---
      Functions
    --- */

    public function queryArgs($query)
    {
      if (!isset($query->query_vars['s'])) return $query;

      $query->query_vars['suppress_filters'] = false;
      return $query;
    }
  }