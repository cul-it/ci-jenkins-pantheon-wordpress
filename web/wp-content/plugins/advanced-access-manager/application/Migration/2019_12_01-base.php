<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

namespace AAM\Migration;

use AAM_Core_API,
    AAM_Core_Migration,
    AAM_Addon_Repository,
    AAM_Core_Contract_MigrationInterface;

/**
 * This migration class that converts add-ons registry
 *
 * @package AAM
 * @version 6.0.5
 */
class Migration610 implements AAM_Core_Contract_MigrationInterface
{

    /**
     * @inheritdoc
     *
     * @version 6.0.5
     */
    public function run()
    {
        // Reset failure log
        AAM_Core_Migration::resetFailureLog();

        $list = AAM_Core_API::getOption(
            AAM_Addon_Repository::DB_OPTION, array(), 'site'
        );

        if (is_array($list)) {
            $converted = array();

            foreach($list as $slug => $data) {
                if (stripos($slug, 'plus') !== false) {
                    $converted['aam-plus-package'] = $data;
                } elseif (stripos($slug, 'hierarchy') !== false) {
                    $converted['aam-role-hierarchy'] = $data;
                } elseif (stripos($slug, 'check') !== false) {
                    $converted['aam-ip-check'] = $data;
                } elseif (stripos($slug, 'complete') !== false) {
                    $converted['aam-complete-package'] = $data;
                } elseif (stripos($slug, 'commerce') !== false) {
                    $converted['aam-ecommerce'] = $data;
                }
            }

            AAM_Core_API::updateOption(
                AAM_Addon_Repository::DB_OPTION, $converted, 'site'
            );
        }

        // Finally store this script as completed
        AAM_Core_Migration::storeCompletedScript(basename(__FILE__));

        return array('errors' => array());
    }

}

if (defined('AAM_KEY')) {
    return (new Migration610())->run();
}