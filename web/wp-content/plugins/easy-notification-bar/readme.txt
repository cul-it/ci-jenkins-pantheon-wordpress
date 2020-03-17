=== Easy Notification Bar ===
Contributors: WPExplorer
Tags: notification, notification bar, notice, notice bar, top bar, banner
Requires at least: 5.2.0
Requires PHP: 5.6.2
Tested up to: 5.3.2
Stable Tag: 1.0
License: GNU Version 2 or Any Later Version

== Description ==
Adds a new section in the WordPress customizer so you can easily enable a notification bar on your site. The plugin allows you to enter your custom notification bar text as well as an optional button to display next to your text. Perfect for notifying visitors of a current sale or hot product.

The [Easy Notification Bar](https://wordpress.org/plugins/easy-notification-bar/) plugin makes use of the newer "wp_body_open" action hook introduced in WordPress 5.2.0 which allows the plugin to work better with any theme that has been updated to support the tag. Contrary to other notice bar solutions which rely on absolute positioning, this plugin inserts the notice bar right after the body tag so it should display perfectly without any conflicts on any well-coded theme.

This is a "static" notification bar at the top of your site. When you scroll down the page it will become hidden which is generally better for usability and [SEO](https://www.wpexplorer.com/wordpress-seo/).

Live Demo: You can view a live demo for the notification bar on our [More Widgets Plugin Demo](https://demo.wpexplorer.com/more-widgets/)

== Features ==

* Easy setup via the WordPress customizer
* Custom background, color, text alignment and font size settings
* Optional callout button
* Responsive design so it looks good on mobile
* Minimal code (no javascript or cookies required)

== Installation ==

1. Go to your WordPress website admin panel
2. Select Plugins > Add New
3. Search for "Easy Notification Bar"
4. Click Install
5. Activate the plugin
6. A default notification should now appear on your site. If it does not then you need to update your theme to work properly (see FAQ).
7. Go Appearance > Customize > Easy Notification Bar to customize your notification

== Frequently Asked Questions ==

= Why doesn't the notification display on my site even though I enabled it? =
This plugin makes use of the WordPress core "wp_body_open" action hook which should be added to every theme header.php file and was introduced in Wordpress 5.2.0. You will need to properly add this action hook to your header.php file and or contact the theme developer so that your theme is updated accordingly.

Feel free to ask in the support forum if you need help updating your header.php file. If you are using a free theme on WordPress.org please link to the theme in question. If you are using a premium theme, contact the developers for support since you paid for it.

= Can I create multiple notifications? =
By default this plugin adds a single notification bar to your entire site. However, you can use the "easy_notification_bar_settings" filter to alter the default settings conditionally. This will allow you to modify the message, button_text, button_link and every other setting via a child theme if you wanted to alter the notification bar for different sections of your site.

= How can I conditionally enable/disable the notification? =
The notification bar will display on your entire site but you can use the "easy_notification_bar_is_enabled" filter to conditionally show/hide the notification bar.

== Changelog ==

= 1.0 =

* First official release
