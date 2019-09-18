== Changelog ==
= 5.5 =
* Added: support for Toolset.
* Added: support for latest ACF plugin version.
* Added: support for multiple parent relationship import.
* Added: ACF Free plugin support.
* Fixed: Repeater custom fields not appearing under the mapping section.
* Fixed: CSV Importer allows more the one post for one-to-one relationship.
* Fixed: Exporter complete fix for ACF Pro(Repeater fields).
* Fixed: Toolset Image type field issue.
* Fixed: Updating the user records automatically update the password even without mapping.
= 5.4 =
* Added: Import, Update and Schedule XML import. 
* Added: Advanced mapping in XML with Table and Tree view.
* Added: New API to import/update with the mapping template.
* Fixed: Export users created in specific period.
* Fixed: Send email on new user import.
* Fixed: Events Manager & Customer Review advanced mapping support
* Fixed: Mapping data loss with advanced mapping in CSV Import.
= 5.3 =
* Added: CMB2 & Custom Field Suite plugin support.
* Added: Scheduled export.
* Added: Duplicate handling in ACF image import.
* Added: Post update with slug.
* Added: Maintenance mode and rollback option.
* Added: WooCommerce latest version support.
* Improved: Mapping session maintenance without data loss.
* Improved: CSV validation.
* Improved: Import serialized data in CSV.
* Improved: Previous mapping template search.
* Improved: Import Post title with special characters.
* Improved: Export with delimeters.
* Fixed: Comment import, duplicate handling, advanced mapping, ACF repeater field and reference tag in content.
= 5.2 =
* Added: Advanced mapping section with easy drag ’n’ drop.
* Fixed: WooCommerce Latest version Support(2.6.14).
* Fixed: Import & Export ACF Pro repeater fields. ( Tested upto ACF Pro version - 5.5.10 ).
* Added: Latest version support on Yoast SEO 4.4
* Added: Latest version support on All In One SEO 2.3.12.1
* Added: Latest version support on Toolset Types 2.2.8
* Added: Latest version support on PODS 2.6.8
* Added: Latest version support on WP-Members 3.1.6.3
* Fixed: Naming issue while registering third party custom fields from mapping section.
* Added: Latest version support on WPeCommerce 3.12.0
* Added: Latest version support on MarketPress Lite 3.2
* Added: WPML support for the latest version 3.6.3.
* Added: Ultimate member plugin support for Users Import.
* Removed: Unwanted warnings in advanced mapping section (when SCRIPT_DEBUG is true).
* Fixed: Serialized data import using normal mapping section
* Fixed: Import price value on WooCommerce module.
* Removed: Warnings on 'admin/views/form-mapping-configuration.php'
* Fixed: Send email while import users with auto generated passwords.
* Fixed: Issue with comments import.
* Fixed: Issue with post status on post types (Static post status).
* Fixed: Issue with duplicate handling.
* Removed: Duplicate handling option for update mode.

= 5.1 =
* Improvements: Performance tuneup.
* Moved: All helper class files under helpers directory.
* Added: WebService for below functionalities.
    -- Field registration for ACF, PODS & Types.
	-- Fetch fields based on the module. Ex:- Posts (or) WooCommerce (or) Events.
	-- Import data to the specific Post type (or) Users, Products, Orders, Variations, Refunds, Coupons, Events, Customer Reviews & Comments, etc.,
	    * Can create record based on the module.
	    * Can update the record information based on the module & ID of the record.
	-- Assign featured image to the specific Post (or) Page (or) Product.
	-- Assign Terms & Taxonomies for the specific Post (or) Page (or) Product.
	-- Get existing mapping template information based on the "Template Name".
	-- Save current mapping as a template.
	-- Fetch record based on the Module & ID of the record.
	    * Code tuneup on Export parser to fetch records based on the module & ID of the record.
* Fixed: PODS fields registration issue using API.
* Fixed: Export user fields issue for Toolset types plugin.
* Fixed: wp_page_template field import issue on import Pages.
* Removed: Warnings in plugin row meta information.
	— Error: ( ! ) Warning: implode(): Invalid arguments passed in C:\wamp64\www\dir\wp-admin\includes\class-wp-plugins-list-table.php on line 812
* Fixed: Widget not appeared for ACF Repeater fields while using ACF Pro add-on.
* Added: Inline image support for the below modules.
    -- Posts
    -- Pages
    -- WooCommerce
    -- WPeCommerce
    -- MarketPress
* Removed: Unwanted DOCTYPE wrapper while import the inline images for post content.
* Removed: Unwanted capabilities on activation.
* Removed: Unwanted queries on deactivation.
* Fixed: Thumbnail generation issue on import featured images.
* Fixed: Featured image support for WooCommerce & MarketPress Variations.
* Fixed: Yoast SEO import support for Terms & Taxonomies.
* Fixed: Issue on exporting Yoast SEO data for Post types & Taxonomies.
* Fixed: External URL support ( If having space in the path ).
* Fixed: WooCommerce product type import ( Simple, Grouped, External, Variation, Simple Subscription & Variable Subscription ).
* Fixed: WooCommerce product meta information import.
* Fixed: post_category and post_tag export issue.
* Fixed: Users custom fields not shown in Mapping Section which is created by ACF Pro.
* Fixed: Broken when SCRIPT_DEBUG is true. [Solved](https://wordpress.org/support/topic/broken-when-script_debug-is-true/).
* Fixed: Warnings in prepare statements (when SCRIPT_DEBUG is true).
* Fixed: Issue in File manager, When switching the revisions.
* Fixed: Issues in import multiple images & multiple files on ACF Pro Repeater field.
* Fixed: ACF free image field & file field import.
* Added: Missing font file.
* Removed: Unwanted widget fields for WPeCommerce Coupons.
* Added: Notice for enable the wp-cron to populate the featured images.
* Added: Warning for Exceeded file size csv.
* Fixed: Issue on exporting Categories and Tags.
* Fixed: Issue while import post type with post ID's.
* Added: Restriction for WP dashboard charts to the user who have the role Author & Editor.
* Fixed: Export Issue for Events,Event recurring and locations in Events Manager plugin.
* Added: Notice for Database Optimization.
* Removed: Console warnings on Mapping section.

= 5.0.3 =
* Added: Support for traditional Chinese characters.
* Fixed: Canonical URL support on All in One SEO data import.
* Fixed: Author/Editor menu visibility issue.
* Fixed: [Assigning categories to post issue](https://wordpress.org/support/topic/ultimate-csv-importer-v5-adds-all-posts-to-uncategorized/)
* Fixed: [Import with Chinese character](https://wordpress.org/support/topic/unable-to-import-posts-with-chinese-characters/)
* Fixed: [Custom taxonomies are mixed up while assign a single term to the posts](https://wordpress.org/support/topic/version-5-03-issues/)
* Fixed: All of a sudden all the custom fields present in the WP installation are added to each single post, even if they are empty;
* Fixed: Inline image support. ( Supports all external URL )
* Fixed: All of the posts are now being added the following without need, before post content:
	<!DOCTYPE html PUBLIC “-//W3C//DTD HTML 4.0 Transitional//EN” “http://www.w3.org/TR/REC-html40/loose.dtd”> <html><body> and <html> after post content;
* Removed: Custom capabilities from the roles to access the plugins. ( Administrator, Author, Editor )
* Applied: iCheck style for "Allow author / editor" option.
* Added: Validation on Enquiry & Newsletter Subscription forms.
* Note Added: In Support form.
* Modified: Plugin action links instead of plugin row meta.
* Removed: Unwanted warnings in migration.

= 5.0.2 =
* Added: Compatibility from PHP 5.3.
* Todo Added: For handling the log file whether exists or not.

= 5.0.1 =
* Fixed: WP Customer Reviews import feature.

= 5.0 =
* Added: Compatibility for WordPress 4.7 and PHP 7.
* Added: Option to replace imported CSV file value with static and dynamic value.
* Added: Image image import from external URL
* Added: Send email to newly imported User with Password Information
* Added: Any Custom Post Type import.
* Added: Post Type import with terms & taxonomies with any depth of parent-child hierarchy.
* Added: Migration for the following versions 4.0.0, 4.1.0, 4.4.0 and 4.5.
* Improved: High speed import with enhanced UI.
* Improved: User role import with capability value or role name in CSV.
