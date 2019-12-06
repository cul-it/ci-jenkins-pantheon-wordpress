<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 *
 * @version 6.0.0
 */

/**
 * URI access service
 *
 * @package AAM
 * @version 6.0.0
 */
class AAM_Service_Uri
{
    use AAM_Core_Contract_RequestTrait,
        AAM_Core_Contract_ServiceTrait;

    /**
     * AAM configuration setting that is associated with the feature
     *
     * @version 6.0.0
     */
    const FEATURE_FLAG = 'core.service.uri.enabled';

    /**
     * Constructor
     *
     * @return void
     *
     * @access protected
     * @version 6.0.0
     */
    protected function __construct()
    {
        if (is_admin()) {
            // Hook that initialize the AAM UI part of the service
            if (AAM_Core_Config::get(self::FEATURE_FLAG, true)) {
                add_action('aam_init_ui_action', function () {
                    AAM_Backend_Feature_Main_Uri::register();
                });
            }

            // Hook that returns the detailed information about the nature of the
            // service. This is used to display information about service on the
            // Settings->Services tab
            add_filter('aam_service_list_filter', function ($services) {
                $services[] = array(
                    'title'       => __('URI Access', AAM_KEY),
                    'description' => __('Manage direct access to the website URIs for any role or individual user. Define either explicit URI or wildcard (with Plus Package addon) as well as how to manage user request (allow, deny, redirect, etc.).', AAM_KEY),
                    'setting'     => self::FEATURE_FLAG
                );

                return $services;
            }, 20);
        }

        if (AAM_Core_Config::get(self::FEATURE_FLAG, true)) {
            $this->initializeHooks();
        }
    }

    /**
     * Initialize URI hooks
     *
     * @return void
     *
     * @access protected
     * @version 6.0.0
     */
    protected function initializeHooks()
    {
        add_action('init', array($this, 'authorizeUri'));
    }

    /**
     * Authorize access to current URI
     *
     * @return void
     *
     * @access public
     * @version 6.0.0
     */
    public function authorizeUri()
    {
        $uri    = wp_parse_url($this->getFromServer('REQUEST_URI'));
        $object = AAM::getUser()->getObject(AAM_Core_Object_Uri::OBJECT_TYPE);
        $params = array();

        if (isset($uri['query'])) {
            parse_str($uri['query'], $params);
        }

        if ($match = $object->findMatch($uri['path'], $params)) {
            if ($match['type'] !== 'allow') {
                AAM_Core_Redirect::execute(
                    $match['type'],
                    array(
                        $match['type'] => $match['action'],
                        'code' => (!empty($match['code']) ? $match['code'] : 307)
                    ),
                    true
                );
            }
        }
    }

}

if (defined('AAM_KEY')) {
    AAM_Service_Uri::bootstrap();
}