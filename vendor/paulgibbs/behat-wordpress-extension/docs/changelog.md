# News

## Unreleased
- tbc.

## [0.8.0] - 2017-09-18
### Added
- Finished implementing database content rollback options; consult documentation.

### Changed
- WordHat now requires PHP 7.0+.
- Renamed "WP-API" driver to "WP-PHP" (backwards-compatible, as long as you have not extended driver classes).
- Website; significant documentation update, especially for on-boarding new users.

### Fixed
- Website; mkdocs-material theme update to latest version (accessibility fixes).
- Various PHPDoc improvements and corrections.

## [0.7.1] - 2017-08-21
### Changed
- `Path` setting can now be a relative or absolute path. Previously, only absolute paths were supported.

## [0.7.0] - 2017-06-30
### Added
- `ContentEditor` and `PostContentEditor` page (element) objects for interacting with TinyMCE elements.
- `EditPostContext`.
- `WidgetContext`.
- Add optional `redirect_to` param to `logIn()`.

### Changed
- Driver interface re-organisation. See [#21](https://github.com/paulgibbs/behat-wordpress-extension/issues/21).

### Fixed
- If a browser window is not open when the `BeforeStep` event is run, then our call to Selenium2Driver::executeScript() will throw an exception.
- Compatibility with WordPress 4.8.
- Toolbar page object: fix calls to `evaluateScript`.
- Strict version requirement for WP-CLI.
- Links and buttons behind the WordPress' Toolbar cannot be interacted with.

### Deprecated
- Rename `is_wordpress_error()` to `isWordPressError()`.

## [0.6.0] - 2017-04-05
### Added
- Initial support for [sensiolabs/behat-page-object-extension](https://github.com/sensiolabs/BehatPageObjectExtension) with support for parts of wp-admin, and the Toolbar.

### Changed
- Documentation corrections; website and PHPDoc.
- WP-CLI driver no longer fails if the command returns text through stdout.
- Travis-CI reports job status to our Slack; join us at https://wordhat.herokuapp.com 😀
- Composer requirements loosened for better compatibility with other projects.

### Fixed
- Travis-CI tasks now succesfully run on Github forks of the project.
- Attempts made to improve intermittent failures with the log-in action when run with Selenium. Work-in-progress.
- Regex correction for `given` block for `ContentContext->thereArePosts()`.

### Deprecated
- `isWordpressError()` moved into `Util` namespace.

## [0.5.0] - 2017-02-08
### Added
- PHPCS rules.
- Scrutinizer-CI integration.

### Changed
- Website; switched from Couscous to MkDocs.
- Documentation.
- Travis-CI tweaks.

### Fixed
- Miscellanous driver fixes, especially WP-CLI over SSH. Again.

## [0.4.0] - 2017-01-30
### Added
- Introduce [sensiolabs/behat-page-object-extension](https://github.com/sensiolabs/BehatPageObjectExtension) for future development.

### Fixed
- Miscellanous driver fixes, especially WP-CLI over SSH.

### Changed
- Documentation.
- Website design and performance improvements.
- Travis-CI improvements.

## [0.3.0] - 2017-01-07
### Added
- Miscellanous driver fixes.
- First pass at Contexts.

### Changed
- Documentation.

## [0.2.0] - 2016-11-26
### Added
- WP-API and blackbox drivers.
- Website/documentation.
- Database import/export methods to drivers.

### Changed
- Adjusted exceptions thrown by DriverManager and Drivers.
- Design adjustments to website.

### Fixed
- Miscellanous WP-CLI driver fixes.

## [0.1.0] - 2016-09-22
### Added
- First working version of basic architecture.

[0.7.1]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.7.0...v0.7.1
[0.7.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.6.0...v0.7.0
[0.6.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.5.0...v0.6.0
[0.5.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.4.0...v0.5.0
[0.4.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/paulgibbs/behat-wordpress-extension/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/paulgibbs/behat-wordpress-extension/commit/a47612c6bfd545f6a1dfa854b5080441d93f4514
