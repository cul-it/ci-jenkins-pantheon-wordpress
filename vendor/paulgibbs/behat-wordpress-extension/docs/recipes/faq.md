# FAQs

If you are new to the project or Behat, we recommend that you first [read through our documentation](https://wordhat.info/). For any questions, feedback, or to contribute, you can get in contact with us via Github or our [Slack](https://wordhat.herokuapp.com).

## Drivers
* If you are using the WP-CLI driver to [connect to a remote WordPress site over SSH](https://make.wordpress.org/cli/handbook/running-commands-remotely/), WordHat assumes the remote server is Linux-like, with a shell that provides [GNU Coreutils](https://www.gnu.org/software/coreutils/coreutils.html).
* To configure WordHat to use a specific driver, set [`default_driver`](/configuration/settings.md) in your `behat.yml`.

## Browsers
* If you are using [Selenium](http://docs.seleniumhq.org/download/) to run Javascript tests, and you access your WordPress site over HTTPS, *and* it has a self-signed certificate, you will need to manually configure the web browser to accept that certificate.

## Selenium
* On a Mac, Selenium is incompatible with the default Apple Java; error messages look like "Unsupported major.minor version 52.0". To fix, [install Oracle Java](https://java.com/en/download/).
* With Selenium, some environments may require a "webdriver"; error messages may refer to "GeckoWebDriver" or "ChromeWebDriver", etc. Good solutions include [vvo/selenium-standalone](https://github.com/vvo/selenium-standalone#command-line-interface) or [joomla-projects/selenium-server-standalone](https://github.com/joomla-projects/selenium-server-standalone), the latter of which we use to [set up Selenium on Travis-CI](https://github.com/paulgibbs/behat-wordpress-extension/blob/5616a60d3a059dab1c21c9a81f7053e9337145ae/bin/travis/selenium.sh).

## Supported platforms
* WordHat supports MacOS, most flavours of Linux, and Windows. We use [Travis-CI](https://travis-ci.org/paulgibbs/behat-wordpress-extension) and [Appveyor](https://ci.appveyor.com/project/PaulGibbs/behat-wordpress-extension) to test on Ubuntu and Windows, respectively, and require modern versions of PHP.

## WordPress
* If your WordPress is installed in a subdirectory, you need to set the `site_url` option to the value of the "WordPress address (URL)" option (found in WordPress > Settings > General). For more information, [consult the WordHat documentation](/configuration/settings.md).
