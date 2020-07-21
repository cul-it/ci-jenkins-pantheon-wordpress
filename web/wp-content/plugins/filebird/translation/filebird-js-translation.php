<?php
class FileBird_JS_Translation
{

    public function __construct()
    {

    }

    public static function get_translation()
    {
        $translation_array = array(
            'move_1_file' => __('Move 1 file', NJT_FILEBIRD_TEXT_DOMAIN),
            'oops' => __('Oops', NJT_FILEBIRD_TEXT_DOMAIN),
            'error' => __('Error', NJT_FILEBIRD_TEXT_DOMAIN),
            'notice' => __('Folder Limit Reached', NJT_FILEBIRD_TEXT_DOMAIN),
            'this_folder_is_already_exists' => __('This folder already exists. Please type another name.', NJT_FILEBIRD_TEXT_DOMAIN),
            'action_failed' => __('Action failed.', NJT_FILEBIRD_TEXT_DOMAIN),
            'error_occurred' => __('Sorry! An error occurred while processing your request.', NJT_FILEBIRD_TEXT_DOMAIN),
            'folder_cannot_be_delete' => __('This folder cannot be deleted.', NJT_FILEBIRD_TEXT_DOMAIN),
            'add_sub_folder' => __('Add sub folder', NJT_FILEBIRD_TEXT_DOMAIN),
            'new_folder' => __('New folder', NJT_FILEBIRD_TEXT_DOMAIN),
            'rename' => __('Rename', NJT_FILEBIRD_TEXT_DOMAIN),
            'remove' => __('Remove', NJT_FILEBIRD_TEXT_DOMAIN),
            'delete' => __('Delete', NJT_FILEBIRD_TEXT_DOMAIN),
            'refresh' => __('Refresh', NJT_FILEBIRD_TEXT_DOMAIN),
            'sort_asc' => __('Sort asc', NJT_FILEBIRD_TEXT_DOMAIN),
            'sort_desc' => __('Sort desc', NJT_FILEBIRD_TEXT_DOMAIN),
            'something_not_correct' => __('Something isn\'t correct here.', NJT_FILEBIRD_TEXT_DOMAIN),
            'this_page_will_reload' => __('This page will be reloaded now.', NJT_FILEBIRD_TEXT_DOMAIN),
            'folder_are_sub_directories' => __('This folder contains subfolders, please delete them first!', NJT_FILEBIRD_TEXT_DOMAIN),
            'are_you_sure' => __('Are you sure?', NJT_FILEBIRD_TEXT_DOMAIN),
            'not_able_recover_folder' => __('You will not be able to recover this folder! But files in this folder are all safe, they are moved to Uncategorized folder.', NJT_FILEBIRD_TEXT_DOMAIN),
            'yes_delete_it' => __('Yes, delete it!', NJT_FILEBIRD_TEXT_DOMAIN),
            'deleted' => __('Deleted', NJT_FILEBIRD_TEXT_DOMAIN),
            'Move' => __('Move', NJT_FILEBIRD_TEXT_DOMAIN),
            'files' => __('files', NJT_FILEBIRD_TEXT_DOMAIN),
            'limit_folder' => __('The FileBird Lite version allows you to manage up to 10 folders.<br>To have unlimited folders, please upgrade to PRO version.</br>
		    	<p class="upgrade-description">✅ Unlimited Folders</br>
		    	✅ Compatible with WP Bakery Page Builder</br>
		    	✅ Compatible with Divi Page Builder</br>
		    	✅ Get Fast Updates</br>
		    	✅ Live Chat Support 24/7</br>
		    	✅ 30-day Refund Guarantee</p>', NJT_FILEBIRD_TEXT_DOMAIN),
            'folder_deleted' => __('Your folder has been deleted.', NJT_FILEBIRD_TEXT_DOMAIN),
            'ok' => __('Upgrade to FileBird Pro now', NJT_FILEBIRD_TEXT_DOMAIN),
            'no_thank' => __('No, thanks.', NJT_FILEBIRD_TEXT_DOMAIN),
            'cancel' => __('Cancel', NJT_FILEBIRD_TEXT_DOMAIN),
        );
        return $translation_array;
    }

}
