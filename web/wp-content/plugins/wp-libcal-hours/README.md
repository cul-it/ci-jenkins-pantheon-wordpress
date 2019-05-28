Library Hours and Status from LibCal for WordPress
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display  open/closed status and the closing time if open or next open time if closed for any given Library or a department within a library from LibCal in your WordPress site using shortcodes.

== Description ==

This plugin provides shortcodes for including libcal status and hours in a few ways:

1. It provides open/closed status at the current time for any library or department within a library. In addition, the plugin provides shortcodes to display either the open until time if the libray is open or the next open time if the library is closed using shortcodes.
2. It provides the weekly widget from LibCal, and you can customize it to show the weekly hours for a library or a department within the library.
3. It provides the monthly widget from LibCal
4. It provides a combined view of the weekly and monthly widgets.
5.
== Installation ==

1. Download the zip file from the GitHub repo.
2. Upload the Plugin to the WordPress website and install it following manual installation producure.
3. The Plugin Adds a LibCal Hours section to the Settings in Wordpress. From the Dropdown, select the library and you are done.

== Usage ==

Library Status and Today's Hours:
1. Use [libcal_status_now] shortcode to display the open/close status for the library. Optionally, pass the lid of a department to display the status for a department within the library [libcal_status_now lid="xxxx"].
2. Use [libcal_hours_today] shortcode to display the open until/next open time for the library. Optionally, pass the lid for a department to display the open until/next open time for a department within the library [libcal_hours_today lid="xxxx"]
3. Optionally, use the php echo shortcode to embed the shortcode in your templates:
`<?php echo do_shortcode('[libcal_status_now]') ?>`
`<?php echo do_shortcode('[libcal_status_now lid="xxxx"]') ?>`
`<?php echo do_shortcode('[libcal_hours_today]') ?>`
`<?php echo do_shortcode('[libcal_hours_today lid="1707"]') ?>`

Weekly, Monthly, and Combined hours Widgets:
1. Weekly hours for the library:  [libcal_hours_weekly] or a department within [libcal_hours_weekly lid="xxxx"]
2. Monthly hours calendar for the library: [libcal_hours_monthly]
3. Combined weekly and monthly hours calendar: [libcal_hours_combined]
