=== JSON Content Importer ===
Contributors: berkux
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22
Tags: json,api,gutenberg,block,webservice,twig,livedata,connect,template,content,opendata,parser,application
Requires at least: 3.0
Tested up to: 5.7
Requires PHP: 5.3.0
Stable tag: 1.3.12
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin to import, cache and display a JSON-Feed / JSON-API: Connect your Wordpress to an API / Webservice and display live JSON-data.


== Description ==

= Display live data from a JSON-feed / API on your wordpress-site! =
Grab JSON from an URL and convert it to HTML on a Wordpress-Page

= JSON Content Importer - API- and Webservice-Connector - Powerful and Simple JSON-Import Plugin: =
* Use a templateengine to display the data from an JSON-Feed.
* Define the url of the JSON-Feed, a template for it and other options like number of displayed items, cachetime etc..
* Cacher with new Option: If a http-API request fails, you can use the maybe cached JSON. Set the radio-button in the plugins settings: what API-problem should be handled how (either a non valid API-http-response or a non JSON-API-response - or both). By default for backwards-compatibility this is switched off. Recommendation: Switch on the plugins-cacher (e. g. some minutes) and select the radiobutton for "If the API-http-answercode is not 200 OR sends invalid JSON: try to use cached JSON" in the plugins options.    
* The templateengine inserts the JSON-data in the template.
* You can either use this as wordpress-shortcode inside a page - whereby some extras like urlencoding can be invoked.
* Or use the Gutenberg Mode: Then you don't have the hassle to put an shortcode together, test it and change it. With a Gutenberg-Block you can test it in realtime and create a shortcode (if you want to stay with shortcodes).

= How to start and help =
* [2 Steps using this plugin, plus: examples and the PRO-Version](http://json-content-importer.com/support/faq/ "Step 1 and 2 using this plugin, plus: examples and the PRO-Version").


= How to example of using the plugin =

[youtube https://www.youtube.com/watch?v=GJGBPvaKZsk]

[youtube https://www.youtube.com/watch?v=t3m0PmNyOHI]

Basic structure of the Shortcode:
'[jsoncontentimporter

* url="http://...json"
* numberofdisplayeditems="number: how many items of level 1 should be displayed? display all: leave empty or set -1"
* urlgettimeout="number: who many seconds for loading url till timeout?"
* basenode="starting point of datasets, the base-node in the JSON-Feed where the data is"
* oneofthesewordsmustbein="default empty, if not empty keywords spearated by ','. At least one of these keywords must be in the created text (here: text=code without html-tags)"
* oneofthesewordsmustbeindepth="default: 1, number: where in the JSON-tree oneofthesewordsmustbein must be?"
]
This is the template:
Any HTML-Code plus "basenode"-datafields wrapped in "{}"
{subloop:"basenode_subloop":"number of subloop-datasets to be displayed"}
Any HTML-Code plus "basenode_subloop"-datafields wrapped in "{}". If JSON-data is HTML add "html" flag like "{fieldname:html}"
{/subloop:"basenode_subloop"}
[/jsoncontentimporter]'

* templates like "{subloop-array:AAAA:10}{text}{subloop:AAAA.image:10}{id}{/subloop:AAAA.image}{/subloop-array:AAAA}" are possible:
one is the recursive usage of "subloop-array" and "subloop".
the other is "{subloop:AAAA.image:10}" where "AAAA.image" is the path to an object. This is fine for some JSON-data.

= Some special add-ons for datafields =
* "{street:purejsondata}": Default-display of a datafield is NOT HTML, but HTML-Tags are converted : use this to use really the pure data from the JSON-Feed
* "{street:html}": Default-display of a datafield is NOT HTML: "&lt;" etc. are converted to "&amp,lt;". Add "html" to display the HTML-Code as Code.
* "{street:htmlAndLinefeed2htmlLinefeed}": Same as "{street:html}" plus "\n"-Linefeeds are converted to HTML-Linebreak
* "{street:ifNotEmptyAddRight:,}": If datafield "street" is not empty, add "," right of datafield-value. allowed chars are: "a-zA-Z0-9,;_-:&lt;&gt;/ "
* "{street:html,ifNotEmptyAddRight:extratext}": you can combine "html" and "ifNotEmptyAdd..." like this
* "{street:ifNotEmptyAdd:,}": same as "ifNotEmptyAddRight"
* "{street:ifNotEmptyAddLeft:,}": If datafield "street" is not empty, add "," left of datafield-value. allowed chars are: "a-zA-Z0-9,;_-:&lt;&gt;/ "
* "{locationname:urlencode}": Insert the php-urlencoded value of the datafield "locationname". Needed when building URLs


= JSON Content Importer PRO =
This free version of "JSON Content Importer" can put together many JSON-Feeds and is flexible with it's template-engine. But sometimes you might need more:

* application building by creating a searchform and connect it to a JSON-API in the background: pass GET-Variables to use a dynamic JSON-Feed-URL ("talk to API / webservice")
* much better and more flexible templateengine: twig
* use it as Widget
* create Custom Post Types
* usage on multisite installations
* store Templates independent of pages
* more Shortcode-Parameters
* executing Shortcodes inside a template and
* more features...

If the free version comes to your limit, I'm looking forward that you [COMPARE PRO and FREE of the JSON Content Importer](http://json-content-importer.com/compare/ "COMPARE PRO and FREE of the JSON Content Importer").


== Installation ==
[youtube https://www.youtube.com/watch?v=sZ0GI2j1tn4]
For detailed installation instructions, please read the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

1. Login to your WordPress installation
2. Install plugin by uploading json-content-importer.zip to `/wp-content/plugins/`.
2. Activate the plugin through the _Plugins_ menu.
3. Klick on "JSON Content Importer" menuentry in the left bar: basic caching-settings and more instructions about usage.
4. Cache folder: WP_CONTENT_DIR.'/cache/jsoncontentimporter'. So "WP_CONTENT_DIR.'/cache/'" must be writable for the http-daemon. The plugin checks this and might aborts with an error-message like dir is missing or not writeable. if so: check permissions of the directories.


== Frequently Asked Questions ==

= Help! I need more information! =
[Check the plugin-website!](http://json-content-importer.com/support/faq/ "Check the plugin-website!")

= What does this plugin do? =
This plugin gives a wp-shortcode for use in a page/blog to import, cache and display JSON-data. Inside wp-shortcode some markups (and attributes like urlencode) are defined to define how to display the data.

= How can I make sure the plugin works? =
See this video and try to experiment: 
[youtube https://www.youtube.com/watch?v=t3m0PmNyOHI]
Create a sample-page and use the wordpress-shortcode "jsoncontentimporter". An example is given in the plugin-configpage and in the "Description"-Section.
there are 2 steps: . access to JSON and convert it to HTML: [Step 1: Get access to the JSON-data](https://json-content-importer.com/support/shortcode-jsoncontentimporterpro/ "Get access to the JSON-data") and [Step 2: Convert JSON to HTML](https://json-content-importer.com/support/very-basic-example/ "Convert JSON to HTML").
The Pro-version comes with a installation-check option.

= Who do I find the proper template for my JSON? =
[Check the plugin-website!](http://json-content-importer.com/support/faq/ "Check the plugin-website!")
Free-Version: [If you're lost: open ticket at wordPress.org](https://wordpress.org/support/plugin/json-content-importer) please provide the JSON-code or link to it (either in the posting or in a mail to the plugin author).

= Don't forget: =
[Donate whatever this plugin is worth for you](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=APWXWK3DF2E22)

= What does this plugin NOT do? =
The plugins templateengine of the free version is focused on some basic JSON-imports.
If this comes to a limit check out the pro-version: There you can use the twig-templatengine, create custom posts out of JSON and many other features.
Your options if this plugin does not work:

* use correct code for this plugin ;-)
* if the above is ok, change the JSON-Input
* [open ticket at wordPress.org](https://wordpress.org/support/plugin/json-content-importer) provide the JSON-code there or an email to the plugin-author.
* [Check the pro-plugin!](http://json-content-importer.com/support/faq/ "Check the pro plugin!")

= Where is this plugin from? =
This plugin is made in munich, bavaria, germany!
Famous for Oktoberfest, FC Bayern Munich, AllianzArena, DLD, TUM, BMW, Siemens, seas, mountains and much more...

== Screenshots ==

1. This screen shows the description and settings-page of the "JSON Content Importer"-Plugin
2. This screen shows the Wordpress-Editor with some [jsoncontentimporter]-code
3. The JCI-Gutenberg Block is in the Widget-Blockarea
4. Add a Gutenberg-Block: Right the settings, left the output

== Changelog ==
= 1.3.12 =
* Importaint speed-up Bugfix for JCI-Gutenberg-Block! If you run your Wordpress with Gutenberg-Blocks (no ClassicEditor-Plugin active) several unneeded libraries are loaded (independend of using a JCI-Block or not). This slows the site and gives bad load-ratings e. g. at Googles Pagespeed ( https://developers.google.com/speed/pagespeed/ ). With this fix the unneeded libraries are not loaded any more and the load-rating should be better. 
* Background-Check for allow_url_fopen in Plugin Options: If allow_url_fopen is not set to TRUE in the PHP-Settings, this might prevent PHP and the plugin to get JSON via http-requests from remote servers (if there no red text, all is ok) 
* Plugin Ok with WP 5.6.1

= 1.3.11 =
* Internationalization added: Available languages are English, German. Feel free to add other languages!
* Shotcodeparam nojsonvalue: If "nojsonvalue=y" the API-Answer is available as {nojsonvalue}, helpful if the API answer is not JSON
* Plugin Ok with WP 5.6

= 1.3.10 =
* Bugfix if subloop's have several unreplaced {...} items
* Plugin Ok with WP 5.5.1

= 1.3.9 =
* Bugfix if a JSON-field is not always there or null (loop stopped if so)
* Placeholders for template: If you want to have curly or square brackets {}[] in the template, this can trouble the shortcode-syntax or the template-parser. Use the placeholders #CBO# (curly brackets open), #CBC# (curly brackets close), #SBO# (square brackets open) and #SBC# (square brackets close) in the template, the placeholders will be replaced by it's bracket-values in the end after parsing.
* Plugin ok with WP 5.4.2

= 1.3.8 =
Compatibility fix: Plugin is now Ok with PHP 7.4

= 1.3.7 =
* New Option: If a http-API request fails, you can use the maybe cached JSON. Set the radio-button in the plugins settings: what API-problem should be handled how (either a non valid API-http-response or a non JSON-API-response - or both). By default for backwards-compatibility this is switched off. Recommendation: Switch on the plugins-cacher (e. g. some minutes) and select the radiobutton for "If the API-http-answercode is not 200 OR sends invalid JSON: try to use cached JSON" in the plugins options.   

= 1.3.6 =
* Bugfix: Add Gutenberg-JS only in the backend (before it was also in the frontend) 

= 1.3.5 =
* New: Added a Quicktag to the Wordpress-Text-Editor to insert the JSONContentImporter-Shortcode incl. an example 

= 1.3.4 =
* New Plugin-Option: Switch off Gutenberg features (maybe a site builder needs that)

= 1.3.3 =
* Bugfix of Bugfix, sorry...

= 1.3.2 =
* Bugfixes: detect Gutenberg and Wordpress 5.0

= 1.3.1 =
* Bugfixes

= 1.3.0 =
* Plugin is ok with the Gutenberg Plugin 3.9.0
* Gutenberg-Mode: With an active Gutenberg Plugin you get an easy example for testing and learing how to use the plugin. If you don't want to use Gutenberg on live-stage: Use it to easy create the shortcode (almost avoid the learning of the shortcode-parameter-syntax)     

= 1.2.20 =
* Plugin is ok with Wordpress 4.9.8
* Plugin is ok with the Gutenberg Plugin 3.6.2
* Plugin is ok with the General Data Protection Regulation (GDPR): The plugin does not collect personal data itself. You may list the plugin in your GDPR-documentation as used software. Maybe the used API uses personal data - then you have to take care of the GDPR.
* Improved debugmode (use debugmode=10 in the shortcode for that)
* Changed the Videolink to a better HowTo-Video
* Added upgradelink to pro plugin in PluginList


= 1.2.19 =
* Plugin is ok with Wordpress 4.9.1
* Adding "debugmode=10" to the Shortcode parameters will show you info about the API-JSON-retrieve and the used template for converting to HTML.
* Ever since the plugin uses the PHP function "mb_check_encoding" to detect a maybe needed character-conversion. Unfortunately some Wordpress/PHP-installations do not have the PHP-optional "Multibyte String"-library required for that. Then the plugin does not work. Now the availablility of "mb_check_encoding" is checked: If it'S not there the conversion-feature is disabled.
* At the Plugin-options you can set a "Bearer"-accesskey for oAuth-Bearer-Authentication. The there defined accesskey-string is sent to the API as "Authorization:Bearer accesskey". Some APIs just need "Authorization:accesskey". So "Bearer " should not sent. To do this add "nobearer " (with one space at the end) at the beginning of the option-field.

= 1.2.18 =
* Plugin is ok with Wordpress 4.7.5
* New option: Add Default Useragent for http-request (some APIs need that)

= 1.2.17 =
* Plugin is ok with Wordpress 4.7
* https instead of http for Plugin-Website
* Remove invalid quotation marks in shortcode-attributes (when copypasting example code this can happen)

= 1.2.16 =
* Plugin is ok with Wordpress 4.6.1
* Plugin is ok with PHP 7.0
* Cleaner Code

= 1.2.15 =
* Plugin is ok with Wordpress 4.5.2
* Added features for coming future addons

= 1.2.14 =
* Plugin is ok with Wordpress 4.5
* Improved description

= 1.2.13 =
* Caching-Bug Fixed: Wordpress 4.4.2 does not create a "/cache/" folder. If "/cache/" is missing, the plugin creates on. This is relevant for totally new Wordpress installations, as older ones should have such a directory.


= 1.2.12 =
* Plugin is ok with Wordpress 4.4
* minor change: prevent direct calls of plugin
* bug fixed: path of cached files is now set ok


= 1.2.11 =
* bugfix: problems with numeric JSON-values and field manipulators like {JSONkey:ifNotEmptyAdd:....}
* beta feature Vers. 0.2: Fixed bug in using oAuth-Bearer-Accesscode for authentification at JSON-Server: Bearer-Code is added to the Request-Header.


= 1.2.10 =
* bugfix: caching now works even with very long URLs
* beta feature: On the Optionpage you can set an oAuth-Bearer-Accesscode for authentification at JSON-Server: This Bearer-Code is added to the Request-Header.


= 1.2.9 =
* new: "{street:purejsondata}": Default-display of a datafield is NOT HTML, but HTML-Tags are converted. If you want to use really the pure data from the JSON-Feed, add ":purejsondata". Try first {street}, then {street:html} and then {street:purejsondata}. In some cases: Take care that "meta http-equiv=content-type..." is set to "utf-8"


= 1.2.8 =
* bugfix: handling of JSON-values with $


= 1.2.7 =
* Wordpress 4.3.1: check - all ok
* bugfix: correct encoding of spaces when using {FIELD:html}
* bugfix: deleting unfilled template-items containing dots


= 1.2.6 =
* Wordpress 4.3: check - all ok
* added feature: remove unfilled template-placeholders {...}
* introducing "JSON Content Importer PRO"

= 1.2.5 =
* Wordpress 4.2.3: check
* minor bugfix regarding attribute "ifNotEmptyAddRight"
* new Pluginwebsite: http://json-content-importer.com/

= 1.2.4 =
* "&amp;" in JSON-Feed-URLs are replaced by "&"

= 1.2.3 =
* added a donated feature: new shortcode-params "oneofthesewordsmustnotbein", "oneofthesewordsmustnotbeindepth". This is for excluding JSON-data from display: When one of the ","-separated keywords at "oneofthesewordsmustnotbein" is found in the textblock, ignore this textblock

= 1.2.2 =
* minor bugfix: JSON-Structures like { "$a": "$b"} can be handled (before: "$" made problems)

= 1.2.1 =
* new feature "{street:htmlAndLinefeed2htmlLinefeed}": Text-Linefeeds of JSON-data are converted to HTML-Linefeeds
* Boolean JSON-Values were ignored before this version. Now the text "true" or "False" is displayed
* Bugfixing Cacher: Timeout-Parameter of cache was not handled right
* Fixed a bug with JSON-Value containing "$"

= 1.2.0 =
* new shortcode-parameter: "oneofthesewordsmustbein" and "oneofthesewordsmustbeindepth"
* filter & hook for third party extensions added: hook "json_content_importer_extension" and filter "json_content_importer_result_root"
* Sourcecode: Classes rearranged
* minor bugfix: number of items in subloop/subloop-array was sometimes ignored and all was displayed
* "made in munich" added (see faq)

= 1.1.2 =
* Bugfix: tags like "{aa/aa}" are ok (previous: error)
* Display JSON-HTML-Data really as HTML. Default: JSON-HTML-Data is displayed not as HTML but as HTML-Text. New in this version: tag-sytax like "{tag:html}" or "{street:html,ifNotEmptyAddRight:extratext}" allows real HTML-display.
* New parameter in "[jsoncontentimporter]"-shortcode: set http-timeout "urlgettimeout". default is 5 seconds (ueful if source-website of JSON is slow)
* Logo of plugin: Wordpress-Logo inserted
* Update of screenshots

= 1.1.1 =
Bugfixes

= 1.1.0 =
Completely rewritten template engine for even better JSON-handling:

* "subloop-array": key should also be in the closing tag, e.g. "{subloop-array:KEY:10}{some_array_field}{/subloop-array:KEY}".
The "subloop-array" without KEY in the closing tag is ok if there is only one "subloop-array" in the template. But if there are more than one "subloop-array" in the template insert the KEY in the closing tag!
Then the template engine can identify the correct JSON-data.

* "subloop": what is above for "subloop-array" is also for "subloop", e.g.  "{subloop:KEY:10}{some_object_field}{/subloop:KEY}"

* templates like "{subloop-array:AAAA:10}{text}{subloop:AAAA.image:10}{id}{/subloop:AAAA.image}{/subloop-array:AAAA}" are possible:
one is the recursive usage of "subloop-array" and "subloop".
the other is "{subloop:AAAA.image:10}" where "AAAA.image" is the path to an object.

* JSON-data with multiple use of arrays can be handled by the template engine

= 1.0.5 =
* Added Screenshots
* Enhanced "subloop-array", new processing of pure string/numeric-array data (before: only string/numeric-data in an object)
* Enhanced FAQs: Added Link to Website for better creating shortcode-markups

= 1.0.4 =
Bugfixes

= 1.0.3 =
Enhanced the template engine for better JSON-handling.

= 1.0.2 =
Initial release on WordPress.org. Any comments and feature-requests are welcome: blog@kux.de



== Upgrade Notice ==
Version 1.3.6: * Gutenberg-Mode: No backend-JS loading in frontend when using Gutenberg-Block