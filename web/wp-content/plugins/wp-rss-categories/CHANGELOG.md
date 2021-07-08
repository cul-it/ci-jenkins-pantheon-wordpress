# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.3.3] - 2019-08-14
### Changed
* Changelog is now written in Markdown, using the SemVer format.
* Notices use wording that is consistent with other add-ons.

### Fixed
* Fixed incorrect HTML for admin notices.
* The "category" shortcode parameter was not working when not using legacy mode.

## [1.3.2] - 2019-04-24
### Changed
* Changed protocol for all links to HTTPS wherever necessary or applicable.

### Fixed
* Fixed compatibility with WP RSS Aggregator v4.13 and later.
* The addon no longer causes errors when activated without WP RSS Aggregator being activated as well.

## [1.3.1] - 2016-08-01
### Changed
* Changed copyright and other info in plugin header.

## [1.3] - 2015-12-30
### Changed
* The licensing system was significantly overhauled.

## [1.2.11] - 2015-11-05
### Added
* New object-oriented updater and autoloader.

## [1.2.10] - 2015-05-18
### Added
* Translations for Polish, Dutch, Czech and Portuguese languages.

### Changed
* Removed licensing code in favour of core, making the plugin lighter.
* Faster license and version checks due to new store logic.

### Fixed
Category archive pages are no longer broken due to rewrite rules.

### Removed
* Unused log file.

## [1.2.9] - 2015-01-15
### Added
* Translation support for new languages.

## [1.2.8] - 2014-12-06
### Changed
* Categories are now also assigned to feed items.
* Added Categories column for the feed items.
* Small improvements for better compatability with the Views plugin.

## [1.2.7] - 2014-08-26
### Changed
* OPML categories are now imported as feed source categories.

### Fixed
* Licensing occasionally become inactive or do not activate.

## [1.2.6] - 2014-04-18
### Fixed
* Categories with a large number of feed sources were ommitting some sources on the front-end.

## [1.2.5] - 2013-01-02
### Changed
* License notices only appear on the main site when using WordPress multisite.

## [1.2.4] - 2013-11-16
### Added
* PressTrends tracking code.

### Fixed
* Site crashes when the add-on is activated and the core is not.

## [1.2.3] - 2013-10-28
### Fixed
* Bad link to licence screen.

## [1.2.2] - 2013-10-22
### Fixed
* A missing file import was generating errors.

## [1.2.1] - 2013-10-20
### Fixed
* Removed anonymous functions for backwards PHP compatibility.
* Drastically reduced the number of queries being run per page.

## [1.2] - 2013-09-19
### Changed
* Changed taxonomy name from 'Categories' to 'Feed Categories'.
* License nag can now be dismissed.

### Fixed
* Fixed version comparison errors when enabling the plugin.

## [1.1] - 2013-08-20
### Fixed
* Feed sources were not all getting assigned to default 'Uncategorized' category.

## [1.0] - 2013-08-08
First release.
