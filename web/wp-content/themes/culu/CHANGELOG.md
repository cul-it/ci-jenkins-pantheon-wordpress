# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v1.5.5] - 2021-05-12
### Changed
- WP REST API: Get more than 100 staff per page [#149](https://github.com/cul-it/wp-cul-theme-culu/issues/149)

### Security
- Bump hosted-git-info from 2.8.8 to 2.8.9 in /vue [#151](https://github.com/cul-it/wp-cul-theme-culu/issues/151)
- Bump lodash from 4.17.20 to 4.17.21 in /vue [#150](https://github.com/cul-it/wp-cul-theme-culu/issues/150)
- Bump url-parse from 1.4.7 to 1.5.1 in /vue [#148](https://github.com/cul-it/wp-cul-theme-culu/issues/148)
- Bump underscore from 1.10.2 to 1.13.1 in /vue [#147](https://github.com/cul-it/wp-cul-theme-culu/issues/147)
- Bump ssri from 6.0.1 to 6.0.2 in /vue [#139](https://github.com/cul-it/wp-cul-theme-culu/issues/139)

### Fixed
- Fix link styles on staff page [#152](https://github.com/cul-it/wp-cul-theme-culu/issues/152)
- Fix css display for theme btn graphic. [#146](https://github.com/cul-it/wp-cul-theme-culu/issues/146)

### Added
- Display alt tags for featured news images [#145](https://github.com/cul-it/wp-cul-theme-culu/issues/145)


## [v1.5.4] - 2021-03-30
### Changed
- Rewrite staff post URLs via redirection #104 [#132](https://github.com/cul-it/wp-cul-theme-culu/issues/132)
- Refactor library hours. [#116](https://github.com/cul-it/wp-cul-theme-culu/issues/116)
- Don't label staff with unit if on unit sites [#115](https://github.com/cul-it/wp-cul-theme-culu/issues/115)

### Fixed
- Fix bg hero image placement after new logo was introduced in the theme. [#133](https://github.com/cul-it/wp-cul-theme-culu/issues/133)
- Remove events scrollbars. [#131](https://github.com/cul-it/wp-cul-theme-culu/issues/131)
- Get email value. [#125](https://github.com/cul-it/wp-cul-theme-culu/issues/125)
- Fix button theme. [#112](https://github.com/cul-it/wp-cul-theme-culu/issues/112)
- Bugfix: Use 0 instead of false as librarin default [#114](https://github.com/cul-it/wp-cul-theme-culu/issues/114)

### Added
- Check if redirection plugin is installed & active [#135](https://github.com/cul-it/wp-cul-theme-culu/issues/135)
- Ensure individual staff are URI addressable [#128](https://github.com/cul-it/wp-cul-theme-culu/issues/128)
- Add building occupancy [#134](https://github.com/cul-it/wp-cul-theme-culu/issues/134)
- Random librarian improvements [#127](https://github.com/cul-it/wp-cul-theme-culu/issues/127)
- Better icon & labels for staff profiles views #117 [#126](https://github.com/cul-it/wp-cul-theme-culu/issues/126)
- Distribute staff photo URL with pushed staff #63 [#123](https://github.com/cul-it/wp-cul-theme-culu/issues/123)
- View attribute for staff shortcode #99 [#124](https://github.com/cul-it/wp-cul-theme-culu/issues/124)

## [v1.5.3] - 2021-03-22
### Changed
- Remove decommissioned emergency banner. [#92](https://github.com/cul-it/wp-cul-theme-culu/issues/92)

### Fixed
- Bump prismjs from 1.21.0 to 1.23.0 in /vue [#95](https://github.com/cul-it/wp-cul-theme-culu/issues/95)
- (origin/new-hours-display, new-hours-display) Bump elliptic from 6.5.3 to 6.5.4 in /vue [#105](https://github.com/cul-it/wp-cul-theme-culu/issues/105)
- Fix layout issues. [#101](https://github.com/cul-it/wp-cul-theme-culu/issues/101)

### Added
- Loader component [#81](https://github.com/cul-it/wp-cul-theme-culu/issues/81)
- Style localist events [#90](https://github.com/cul-it/wp-cul-theme-culu/issues/90)
- Add new CUL logo. [#97](https://github.com/cul-it/wp-cul-theme-culu/issues/97)
- Add featured librarian for Mann homepage or other units. [#98](https://github.com/cul-it/wp-cul-theme-culu/issues/98)
- Featured news slider. [#106](https://github.com/cul-it/wp-cul-theme-culu/issues/106)
- Display library equipment. [#107](https://github.com/cul-it/wp-cul-theme-culu/issues/107)
- Add librarian ACF. [#110](https://github.com/cul-it/wp-cul-theme-culu/issues/110)

## [v1.5.2] - 2021-01-22
### Changed
- Use production WP REST API instance for staff profiles [#78](https://github.com/cul-it/wp-cul-theme-culu/issues/78)

### Fixed
- Fix some style issues in Elementor content [#76](https://github.com/cul-it/wp-cul-theme-culu/issues/76)

## [v1.5.1] - 2021-01-21
### Changed
- Fix Elementor styles for section containers [#74](https://github.com/cul-it/wp-cul-theme-culu/issues/74)

## [v1.5.0] - 2021-01-20
### Added
- Staff profiles in Vue [#43](https://github.com/cul-it/wp-cul-theme-culu/issues/43)
- Standardized code format [#38](https://github.com/cul-it/wp-cul-theme-culu/issues/38)
- Create language expertise taxonomy and create new staff ACF with this new taxonomy. [#37](https://github.com/cul-it/wp-cul-theme-culu/issues/37)
- Add Elementor widget alert. [#36](https://github.com/cul-it/wp-cul-theme-culu/issues/36)

### Changed
- Make title field not required for staff profiles [#71](https://github.com/cul-it/wp-cul-theme-culu/issues/71)
- Change Clear Filter link color
- Override Elementor sections padding and width (100%). [#40](https://github.com/cul-it/wp-cul-theme-culu/issues/40)

### Security
- Bump @vue/cli-service; address 3 security alerts [#68](https://github.com/cul-it/wp-cul-theme-culu/issues/68)
- Bump highlight.js from 9.18.1 to 9.18.5 in /vue [#65](https://github.com/cul-it/wp-cul-theme-culu/issues/65)

## [v1.4.2] - 2020-08-14
### Fixed
- Use $_SERVER instead of $_ENV when determining active unit

## [v1.4.1] - 2020-08-13
### Fixed
- Loosen dependency to allow for symfony/yaml 4

### Security
- Use terminus secrets for LibCal auth [#29](https://github.com/cul-it/wp-cul-theme-culu/issues/29)

## [v1.4.0] - 2020-08-12
### Changed
- Add/Update fields in ACF staff group [#22](https://github.com/cul-it/wp-cul-theme-culu/issues/22)
- Purge css reset styles but not core vuetify styles [#23](https://github.com/cul-it/wp-cul-theme-culu/issues/23)
- Drop Mann SmartMap integration from software list [#17](https://github.com/cul-it/wp-cul-theme-culu/issues/17)
- Cache sassafras data via WP transients API [#15](https://github.com/cul-it/wp-cul-theme-culu/issues/15)
- Yaml config file [#12](https://github.com/cul-it/wp-cul-theme-culu/issues/12)
- Add cursor pointer to icon and button search. [#14](https://github.com/cul-it/wp-cul-theme-culu/issues/14)
- Add styles for alert messages (success. info, warning, error). [#11](https://github.com/cul-it/wp-cul-theme-culu/issues/11)
- Use Sassafras as data source for software availability  [#8](https://github.com/cul-it/wp-cul-theme-culu/issues/8)
- Scoped styles for Vue components [#9](https://github.com/cul-it/wp-cul-theme-culu/issues/9)

### Fixed
- Fix software title alpha sort via localeCompare() [#16](https://github.com/cul-it/wp-cul-theme-culu/issues/16)
- Fix Draw Attention and FacetWP Query issue when both are active. [#19](https://github.com/cul-it/wp-cul-theme-culu/issues/19)

### Security
- Bump prismjs from 1.20.0 to 1.21.0 in /vue [#27](https://github.com/cul-it/wp-cul-theme-culu/issues/27)
- Bump elliptic from 6.5.2 to 6.5.3 in /vue [#26](https://github.com/cul-it/wp-cul-theme-culu/issues/26)
- Bump lodash from 4.17.15 to 4.17.19 in /vue [#18](https://github.com/cul-it/wp-cul-theme-culu/issues/18)

## [v1.3.0] - 2020-06-11
### Changed
- Ensure Vue assets are referenced from parent CULU theme, even when a child theme is active [#6](https://github.com/cul-it/wp-cul-theme-culu/issues/6)
- Bump theme version reported in Wordpress admin to match release version maintained via git tagging

## [v1.3.0-beta] - 2020-06-10
### Added
- New repository with full(er) commit history (extracted from Pantheon instance used for dev)
- Integrate bundled Vue apps & port Mann software availability as first example [#2](https://github.com/cul-it/wp-cul-theme-culu/issues/2)

### Changed
- Use SSL for Image Loader library
- Bump websocket-extensions from 0.1.3 to 0.1.4 [#3](https://github.com/cul-it/wp-cul-theme-culu/issues/3)

## [v1.2.0] - 2020-05-11
### Added
- Add mininum height to profile cards
- Style easy notification banner
- Fix isotope layout for for staff profile page
- Update culu plugings
- Enable custom loops to be selected on other pantheon enviroments
- Add exmpty alt tag to staff profile pages and single profiles

## [v1.1.9] - 2020-04-30
### Added
- FileBird – WordPress Media Library Folders Plugin
- Advanced Custom Fields: Image Aspect Ratio Crop Field Plugin
- Remove Staff profile template and template parts
- Add Isotope js library
- Add Isotope to staff profile pages
- Update theme plugins
- Update WP core to 5.4.1
- Update ACF Staff and add image crop component

## [v1.1.8] - 2020-04-16
### Added
- Elementor heading style changes
- Remove Staff templage page
- Remove Staff single template page

## [v1.1.7] - 2020-04-09
### Added
- Update print styles.

## [v1.1.6] - 2020-04-08
### Added
- Make notification banner accessible
- Fix layout issues with Staff page.

## [v1.1.5] - 2020-04-06
### Added
- Test Wordpress 5.4
- Fix header hero photo placement after adding alert banner.
- Update libcal plugin and header changes introduced by the plugin.
- Remove the_posts_navigation() and add culu_pagination() function.
- Make UI pagination consistent.
- Plugin Updates.
- Change Ask a Librarian link.

## [v1.1.4] - 2020-03-17
### Added
- Change notification banner position.

## [v1.1.3] - 2020-03-16
### Added
- Style single page layout for elementor single page templates.
- Fix comment typo on custom-header.php
- Add Easy Notification Bar plugin
- Fix layout issues introduced when easy notification bar banner is active.
- Update Emergency Banner code.

## [v1.1.2] - 2020-03-13
### Changed
- Add async attribute to emergency banner

## [v1.1.1] - 2020-02-25
### Changed
- Fix on UL and OL lists font size increase with indentation.

## [v1.1.0] - 2020-02-20
### Changed
- bump version number

## [v1.0.32] - 2020-02-19
### Added
- Test Libcal API for Equipment (read only)
- Test Libcal API for Spaces (read only)
- Update WP core to 5.3.1
- Update plugins
- Fix background header on interior pages
- Improve search display
- Remove all-in-one-migration plugin
- Allow search on highlight carousel component content
- Fix padding on search display container
- Update ACF templates for staff profile including a few search fields
- Add relevanssi plugin

## [v1.0.31] - 2019-12-12
### Changed
- Last minute changes - bump the version number
- Override some elementor styles.

## [v1.0.30] - 2019-12-12
### Added
- Override some elementor styles.

## [v1.0.29] - 2019-12-11
### Added
- HTML validation
- Align Orcid and Linkedin logos.

## [v1.0.28] - 2019-12-06
### Added
- Bump the version number

## [v1.0.27] - 2019-12-05
### Added
- Enqueue culu theme style
- Add space between CUL header and hero header unit
- Style and make search form generated by WP accessible when the is no search results
- Fix search filter spacing and alignment
- Add same color label scheme to tags and categories for search display, blog pages, and blog page.
- Add category and tags labels to blog page

### Changed
- Remove post author on search result display.
- Remove Uncategorized tag from search result display.

## [v1.0.26] - 2019-11-21
### Changed
- Update Staff Profiles so that only Name and Title are required
- Format footer.php
- Plugin updates
- Update to WP 5.3

### Added
- Add proper textdomain to theme functions and template parts
- Fed staff profile consultation ACF select list with all the user appointments from libcal API, so users can enable it
- Make heading highlights consistent across all units

## [v1.0.25] - 2019-11-06
### Changed
- Fix layout for ui search on mobile devices for home and interior pages.
- Change Catalog filter on search to Library resources.
- Fix layout on seach after adding Library resources new filter label.
- Remove full hours template and customizer related options.
- Remove extra space when secondary units contact is not available.

### Added
- Add to the footer the right phone
- Add Copyright footer symbol
- Add secondary contact information to footer.
- Add CULU overrides to Elementor

## [v1.0.24] - 2019-10-24
### Changed
- Fix main nav link display

## [v1.0.23] - 2019-10-22
### Added
- Add Ares latest plugin
- Add Siteimprove plugin
- Update wordpress to 5.2.4
- Add BV pantheon migration plugin
- Enqueue Raleway google font
- Enqueue custom child theme css
- Elementor css override
- Clean up category.css
- Update header template on culu for adding a template part for - unit college logo

### Changed
- Adjust footer contact wrapping
- Make college height same height for all units
- Fix nav on mobile.
- Add more width to main submenus
- Structure css for category and remore HR for last post
- Make post headers title H2s
- Style staff name and degree after changing Hs markup
- Style tag template
- Format style.css on culu.

## [v1.0.22] - 2019-10-09
### Added
- Audit theme accessibility.
- Full theme accessibility.
- Ares plugin accessibility. Style content.
- LibCal plugin accessibility. Style content.
- Staff Bio.
- HTML validation.
- CSS valivation.

### Changed
- Fix main nav overlap with consultation overlay.
- Search logic to accommodate different filters on units sites.

[Unreleased]: https://github.com/cul-it/wp-cul-theme-culu/compare/v1.5.5...HEAD
[v1.5.5]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.5
[v1.5.4]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.4
[v1.5.3]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.3
[v1.5.2]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.2
[v1.5.1]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.1
[v1.5.0]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.5.0
[v1.4.2]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.4.2
[v1.4.1]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.4.1
[v1.4.0]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.4.0
[v1.3.0]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.3.0
[v1.3.0-beta]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.3.0-beta
[v1.2.0]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.2.0
[v1.1.9]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.9
[v1.1.8]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.8
[v1.1.7]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.7
[v1.1.6]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.6
[v1.1.5]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.5
[v1.1.4]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.4
[v1.1.3]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.3
[v1.1.2]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.2
[v1.1.1]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.1
[v1.1.0]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.1.0
[v1.0.32]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.32
[v1.0.31]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.31
[v1.0.30]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.30
[v1.0.29]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.29
[v1.0.28]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.28
[v1.0.27]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.27
[v1.0.26]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.26
[v1.0.25]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.25
[v1.0.24]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.24
[v1.0.23]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.23
[v1.0.22]: https://github.com/cul-it/wp-cul-theme-culu/releases/tag/v1.0.22
