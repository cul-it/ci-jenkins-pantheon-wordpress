## site-testing

cucumber tests for web sites
These are some variations of the command line:

```
bundle exec cucumber SITE=ci-jenkins-pantheon-wordpress.pantheonsite.io STAGE=dev HTTPS=1 HEADLESS=1
bundle exec cucumber SITE=www.library.cornell.edu STAGE=prod HTTPS=1
bundle exec cucumber SITE=www.library.cornell.edu STAGE=dev HEADLESS=1 --tags @site_up
bundle exec cucumber SITE=www.library.cornell.edu STAGE=prod HEADLESS=1 HEADLESS_BROWSER=headless_chrome
bundle exec cucumber SITE=www.library.cornell.edu STAGE=test HEADLESS=1 POLTERGEIST_DEBUG=1
```

Add new sites to site-testing.ini

### Arguments (default)
```
SITE ()
STAGE ()
HEADLESS () - without this it defaults to chrome but there's no chrome driver on Jenkins
HEADLESS_BROWSER (0)
POLTERGEIST_DEBUG (0)
SUBMIT (0) - 1: click the submit button on forms
HTTPS (0) - 1: use https:// protocol (to use http, leave the argument out)
```

### Stages

* `dev`
* `test`
* `live` - (live-xyzcornelledu.panthion.io)
* `prod` - (actual domain name)
* `ci-123` - (multidev)

These are set up in site-testing.ini

### Headless Browsers

* `poltergeist` (default)
* `selenium_chrome_headless`
* `headless_chrome`


### Piwik problem

You may see a failed test like this:

```
Given I go to the home page               # features/step_definitions/custom_steps.rb:66
      Request to 'https://wwwtest.library.cornell.edu' failed to reach server, check DNS
      and/or server status - Timed out with the following resources still waiting
      https://webstats.library.cornell.edu/piwik.js (Capybara::Poltergeist::StatusFailError)
```

Sites using the piwik module will not be able to be loaded by these tests,
*unless*
you go to

```
/admin/config/system/piwik
```

under Advanced Settings and check off

```
☑︎ Locally cache tracking code file
```

### Antibot problem

The Antibot module checks for javascript - be sure the diver supports it.

### Honeypot problem

https://www.drupal.org/project/webform/issues/2906236
Honeypot complains if it took less than 5 sconds to fill out the form.
The 5 seconds is configurable, so you may need to adjust per site.

