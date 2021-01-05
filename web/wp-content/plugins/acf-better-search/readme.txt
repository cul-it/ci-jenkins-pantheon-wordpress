=== ACF: Better Search ===
Contributors: mateuszgbiorczyk
Donate link: https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-donate
Tags: acf search, advanced custom fields, better search, extended search, search
Requires at least: 5.0
Tested up to: 5.6
Requires PHP: 7.0
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.

== Description ==

This plugin adds to default WordPress search engine the ability to search by content from selected fields of Advanced Custom Fields plugin.

Everything works automatically, no need to add any additional code. The plugin does not create a search results page, but modifies the SQL database query to make your search engine work better.

Additionally you can search for whole phrases instead of each single word of phrase. As a result, search will be more accurate than before.

#### New search core

We modified the code of search engine. Content search is now faster by about 75% *(depending on the level of complexity of searched phrase)*!

#### Support to the development of plugin

We spend hours working on the development of this plugin. Technical support also requires a lot of time, but we do it because we want to offer you the best plugin. We enjoy every new plugin installation.

If you would like to appreciate it, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-content). **If every user bought at least one, we could work on the plugin 24 hours a day!**

#### Please also read the FAQ below. Thank you for being with us!

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/acf-better-search` directory, or install plugin through the WordPress plugins screen directly.
2. Activate plugin through `Plugins` screen in WordPress Admin Panel.
3. Use `Settings -> ACF: Better Search` screen to configure the plugin.

== Frequently Asked Questions ==

= What version of Advanced Custom Fields is supported? =

Advanced Custom Fields in version 5 *(also free)*. ACF below version 5 has a different data structure in database and is not supported.

= In what fields does the plugin search? =

Our plugin supports the following fields: Text, Text Area, Number, Email, Url, File, Wysiwyg Editor, Select, Checkbox and Radio Button.

All these fields may be located in both the Repeater or Flexible Content field.

= How does this work? =

Plugin changes all SQL queries by extending the standard search to selected fields of Advanced Custom Fields.

The plugin in admin panel works same as for the search page.

It works for `WP_Query` class.

= How to activate advanced search? =

Everythings works automatically. For custom `WP_Query` loop and `get_posts()` function also if you add [Search Parameter](https://codex.wordpress.org/Class_Reference/WP_Query#Search_Parameter).

= What to do when not searching for posts? =

Sometimes it happens that the data in your database is incorrectly arranged. This happens when you import or duplicate posts.

You can use `Incorrect Mode`. This is a slower search, but it does not take into account the order of records in the `_postmeta` table. This solution should help in this situation. Use of this mode is allowed without restrictions. This does not mean any problems with your website.

= How does searching for whole phrases? =

The default search in WordPress is to search for each of words listed. An additional option in the plugin settings allows you to search for occurrences of the whole phrase entered in the search field without word division.

You can enable it at any time.

= How does Lite mode work? =

In this mode, the plugin does not check the field types. Phrases are searched in all ACF fields. Thanks to this, the query to the database is smaller and faster by about 25%. However, we do not have control over which fields are taken into account when searching.

= Is the plugin completely free? =

Yes. The plugin is completely free.

However, working on plugins and technical support requires many hours of work. If you want to appreciate it, you can [provide us a coffee](https://ko-fi.com/gbiorczyk/?utm_source=acf-better-search&utm_medium=readme-faq). Thanks everyone!

Thank you for all the ratings and reviews.

If you are satisfied with this plugin, please recommend it to your friends. Every new person using our plugin is valuable to us.

This is all very important to us and allows us to do even better things for you!

== Screenshots ==

1. Screenshot of the options panel

== Changelog ==

= 3.7.0 (2020-12-22) =
* `[Changed]` Regular expressions in SQL query to Henry Spencer's implementation
* `[Added]` Filter `acfbs_sql_where` to change WHERE part of SQL query
* `[Added]` Filter `acfbs_sql_join` to change INNER JOIN part of SQL query

= 3.6.0 (2020-10-28) =
* `[Changed]` Required PHP version to 7.0

= 3.5.3 (2020-04-05) =
* `[Removed]` Support for inverted values in `_postmeta` table
* `[Added]` Support for incorrect data structure in `_postmeta` table *(fixes search for imported and duplicated posts)*

= 3.5.2 (2020-03-31) =
* `[Fixed]` Displaying new values after saving settings
* `[Fixed]` Support for empty array returned by `acfbs_search_post_object_fields` filter
* `[Changed]` Static methods for filters `posts_join`, `pre_get_posts`, `posts_request` and `posts_search`
* `[Added]` Support for inverted values in `_postmeta` table *(fixes search for imported posts)*

= 3.5.1 (2020-03-19) =
* `[Fixed]` Search based only on `post_title`, `post_content` or `post_excerpt`

= 3.5.0 (2020-03-11) =
* `[Changed]` Improved SQL query performance
* `[Added]` The ability to search whole words
* `[Added]` Support for field name prefixes other than `field_`

= 3.4.3 (2019-10-03) =
* `[Added]` Filter `acfbs_search_post_object_fields` to select post fields using to search

= 3.4.2 (2019-09-26) =
* `[Fixed]` Fix for `posts_join` filter
* `[Changed]` Update priority of `posts_search` filter *(from 10 do 0)*

= 3.4.1 (2019-09-20) =
* `[Added]` Filter `acfbs_search_is_available` to block search

= 3.4.0 (2019-09-16) =
* `[Added]` New way to start search engine
* `[Added]` Filter `acfbs_is_available` to turn off search engine

= 3.3.2 (2019-07-23) =
* `[Added]` Validation for fields types on settings page

= 3.3.1 (2019-06-26) =
* `[Fixed]` Security changes

= 3.3.0 (2019-06-17) =
* `[Changed]` Settings page
* `[Changed]` Admin notice

= 3.2.0 (2019-05-27) =
* `[Changed]` Plugin structure
* `[Added]` Mode to ability using only selected fields for searching
* `[Added]` Filters to extend plugin capabilities

= 3.1.3 (2019-03-29) =
* `[Fixed]` Full path for loaded PHP files

= 3.1.2 (2018-10-24) =
* `[Added]` Support for free version of ACF 5

= 3.1.1 (2018-10-22) =
* `[Added]` Default hidden admin notice

= 3.1.0 (2018-10-18) =
* `[Changed]` Improved search engine
* `[Changed]` Settings page
* `[Added]` Support for AUTO_INCREMENT field in database other than 1
* `[Added]` Possibility of permanent turn off admin notice

= 3.0.1 (2018-04-18) =
* `[Removed]` Support for old WordPress versions
* `[Fixed]` Other changes

= 3.0.0 (2018-04-13) =
* `[Changed]` Plugin structure
* `[Added]` Support for `get_posts()` function
* `[Added]` Search in Admin Panel
* `[Added]` Support for internationalization

= 2.2.0 (2018-02-26) =
* `[Added]` Cleaning database after removing plugin

= 2.1.3 (2018-01-30) =
* `[Fixed]` Compatibility for Polylang plugin

= 2.1.2 (2018-01-15) =
* `[Added]` Support for File type field

= 2.1.1 (2017-12-21) =
* `[Changed]` Admin notice

= 2.1.0 (2017-11-06) =
* `[Changed]` Small changes
* `[Added]` Lite mode for faster seach

= 2.0.7 (2017-08-24) =
* `[Fixed]` Turn off plugin core while searching uploads media items

= 2.0.6 (2017-08-20) =
* `[Added]` Support for WordPress Multisite

= 2.0.5 (2017-07-25) =
* `[Fixed]` Closing notice in Admin panel

= 2.0.4 (2017-07-20) =
* `[Changed]` Access to settings page
* `[Added]` Support for WP AJAX

= 2.0.3 (2017-07-15) =
* `[Fixed]` Text search with apostrophe and quotation marks

= 2.0.2 (2017-06-29) =
* `[Removed]` Support for `get_posts()`

= 2.0.1 (2017-06-16) =
* `[Fixed]` Support for PHP 7

= 2.0.0 (2017-06-15) =
* `[Changed]` Search engine
* `[Changed]` Settings page
* `[Added]` Notifications in admin panel
* `[Added]` The ability to search whole phrases

= 1.0.0 (2016-12-26) =
* The first stable release

== Upgrade Notice ==

None.