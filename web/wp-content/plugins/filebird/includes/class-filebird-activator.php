<?php

/**
 * Fired during plugin activation
 *
 * @link       https://ninjateam.org
 * @since      1.0.0
 *
 * @package    FileBird
 * @subpackage FileBird/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    FileBird
 * @subpackage FileBird/includes
 * @author     Ninja Team <support@ninjateam.org>
 */
class FileBird_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        self::checkReviewStatus();
    }

    public static function checkReviewStatus()
    {
        $option = get_option('njt_FileBird_review');
        if ($option){
            update_option('njt_FileBird_review', 'show'); //Show now
        }else{
            update_option('njt_FileBird_review', time() + 2*60*60*24); //After 2 days show
        }
    }
}
