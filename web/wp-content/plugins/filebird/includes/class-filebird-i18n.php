<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://ninjateam.org
 * @since      1.0.0
 *
 * @package    FileBird
 * @subpackage FileBird/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    FileBird
 * @subpackage FileBird/includes
 * @author     Ninja Team <support@ninjateam.org>
 */
class FileBird_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        // load_plugin_textdomain(
        //     NJT_FILEBIRD_TEXT_DOMAIN,
        //     false,
        //     NJT_FILEBIRD_TEXT_DOMAIN . '/languages'
        // );
        $current_user = wp_get_current_user();

        if (!($current_user instanceof WP_User)) {
            return;
        }

        if (function_exists('get_user_locale')) {
            $language = get_user_locale($current_user);
        } else {
            $language = get_locale();
        }
        load_textdomain("filebird", NJT_FILEBIRD_PLUGIN_PATH . '/languages/' . $language . '.mo');
    }
}
