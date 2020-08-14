# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/cul-it/wp-cul-theme-culu/compare/v1.4.1...HEAD
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