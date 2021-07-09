<?php

/**
 * Common URL redirects shared across all sites
 * -- managed via Redirection plugin
 *
 * @package culu
 */

/**
 * Hook into admin initialization to add redirects via Redirection's PHP API
 * -- https://redirection.me/developer/php-api/
 *
 */
add_action('admin_init', 'create_redirects');

function create_redirects()
{
    // Only proceed if Redirection plugin is installed & active
    if (is_plugin_active('redirection/redirection.php')) {
        $url = '^/staff/(.*)';
        $filter_params = [
            'filterBy' => [
                'url' => $url,
            ]
        ];
        $already_exists = Red_Item::get_filtered($filter_params);

        if ($already_exists['total'] == 0) {
            $params = [
                'url' => $url,
                'match_url' => 'regex',
                'match_data' => [
                    'source' => [
                        'flag_query' => 'exact',
                        'flag_case' => false,
                        'flag_trailing' => false,
                        'flag_regex' => true
                    ]
                ],
                'action_code' => 301,
                'action_type' => 'url',
                'action_data' => [
                    'logged_in' => '',
                    'logged_out' => '/staff-profiles#$1'
                ],
                'match_type'  => 'login',
                'title' => 'Use staff profiles Vue app when logged out users request individual staff',
                'regex'  => true,
                'group_id' => 1,
                'enabled' => true,
            ];

            Red_Item::create($params);
        }
    }
}
