<?php

  namespace AcfBetterSearch\Search;

  class Query
  {
    public function __construct()
    {
      add_filter('pre_get_posts', ['AcfBetterSearch\Search\Query', 'queryArgs']); 
    }

    /* ---
      Functions
    --- */

    public static function queryArgs($query)
    {
      if (!isset($query->query_vars['s'])) return $query;

      $query->query_vars['suppress_filters'] = false;
      return $query;
    }
  }