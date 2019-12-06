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
 * API route object
 *
 * @package AAM
 * @version 6.0.0
 */
class AAM_Core_Object_Route extends AAM_Core_Object
{

    /**
     * Type of object
     *
     * @version 6.0.0
     */
    const OBJECT_TYPE = 'route';

    /**
     * @inheritdoc
     * @version 6.0.0
     */
    protected function initialize()
    {
        $option = $this->getSubject()->readOption('route');

        $this->determineOverwritten($option);

        // Trigger custom functionality that may populate the menu options. For
        // example, this hooks is used by Access Policy service
        if (empty($option)) {
            $option = apply_filters('aam_route_object_option_filter', $option, $this);
        }

        $this->setOption(is_array($option) ? $option : array());
    }

    /**
     * Check if route is restricted
     *
     * @param string $type   REST or XMLRPC
     * @param string $route
     * @param string $method
     *
     * @return boolean
     *
     * @access public
     * @version 6.0.0
     */
    public function isRestricted($type, $route, $method = 'POST')
    {
        $options = $this->getOption();
        $id      = strtolower("{$type}|{$route}|{$method}");

        return !empty($options[$id]);
    }

}