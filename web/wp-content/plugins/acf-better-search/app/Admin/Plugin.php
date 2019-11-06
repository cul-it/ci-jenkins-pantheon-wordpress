<?php

  namespace AcfBetterSearch\Admin;

  class Plugin
  {
    public function __construct()
    {
      add_filter('network_admin_plugin_action_links_' . ACFBS_NAME, [$this, 'addPluginLinks']);
      add_filter('plugin_action_links_' . ACFBS_NAME,               [$this, 'addPluginLinks']);
    }

    /* ---
      Functions
    --- */

    public function addPluginLinks($links)
    {
      array_unshift($links, sprintf(
        __('%sSettings%s', 'acf-better-search'),
        '<a href="' . menu_page_url('acfbs_admin_page', false) . '">',
        '</a>'
      ));
      $links[] = sprintf(
        __('%sProvide us a coffee%s', 'acf-better-search'),
        '<a href="https://ko-fi.com/gbiorczyk/" target="_blank">',
        '</a>'
      );
      return $links;
    }
  }