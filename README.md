# Example WordPress Composer

## skip down to the bottom for [Jenkins Workflow](#workflow)

[![CircleCI](https://circleci.com/gh/pantheon-systems/example-wordpress-composer.svg?style=svg)](https://circleci.com/gh/pantheon-systems/example-wordpress-composer)

This repository is a start state for a Composer-based WordPress workflow with Pantheon. It is meant to be copied by the the [Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin) which will set up for you a brand new

* GitHub repo
* Free Pantheon sandbox site
* A CircleCI configuration to run tests and push from the source repo (GitHub) to Pantheon.

For more background information on this style of workflow, see the [Pantheon documentation](https://pantheon.io/docs/guides/github-pull-requests/).


## Installation

#### Prerequisites

Before running the `terminus build:project:create` command, make sure you have all of the prerequisites:

* [A Pantheon account](https://dashboard.pantheon.io/register)
* [Terminus, the Pantheon command line tool](https://pantheon.io/docs/terminus/install/)
* [The Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin)
* An account with GitHub and an authentication token capable of creating new repos.
* An account with CircleCI and an authentication token.

You may find it easier to export the GitHub and CircleCI tokens as variables on your command line where the Build Tools Plugin can detect them automatically:

```
export GITHUB_TOKEN=[REDACTED]
export CIRCLE_TOKEN=[REDACTED]
```

#### One command setup:

Once you have all of the prerequisites in place, you can create your copy of this repo with one command:

```
terminus build:project:create pantheon-systems/example-wordpress-composer my-new-site --team="Agency Org Name"
```

The parameters shown here are:

* The name of the source repo, `pantheon-systems/example-wordpress-composer`. If you are interest in other source repos like Drupal 8, see the [Terminus Build Tools Plugin](https://github.com/pantheon-systems/terminus-build-tools-plugin).
* The machine name to be used by both the soon-to-be-created Pantheon site and GitHub repo. Change `my-new-site` to something meaningful for you.
* The `--team` flag is optional and refers to a Pantheon organization. Pantheon organizations are often web development agencies or Universities. Setting this parameter causes the newly created site to go within the given organization. Run the Terminus command `terminus org:list` to see the organizations you are a member of. There might not be any.

#### PHP Version

You will need PHP 7.0 or higher locally to run the `build:project:create` command as some of the dependencies in this project require it. Both `composer.json` and `pantheon.yml` are currently set to use PHP 7.0.

## Important files and directories

#### `/web`

Pantheon will serve the site from the `/web` subdirectory due to the configuration in `pantheon.yml`, facilitating a Composer based workflow. Having your website in this subdirectory also allows for tests, scripts, and other files related to your project to be stored in your repo without polluting your web document root.

#### `/web/wp`

Even within the `/web` directory you may notice that other directories and files are in different places [compared to a default WordPress installation](https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory). See `/web/wp-config.php` for key settings like `WP_SITEURL` which allows WordPress core to be relocated to `/web/wp`. The overall layout of directories in the repo is inspired by [Bedrock](https://github.com/roots/bedrock).

#### `composer.json`

If you are just browsing this repository on GitHub, you may not see some of the directories mentioned above like `wp-admin`. That is because WordPress core and its plugins are installed via Composer and ignored in the `.gitignore` file. Specific plugins are added to the project via `composer.json` and `composer.lock` keeps track of the exact version of each plugin (or other dependency). Generic Composer dependencies (not WordPress plugins or themes) are downloaded to the `/vendor` folder. Use the `require` section for any dependencies you wish to push to Pantheon, even those that might only be used on non-Live environments. Dependencies added in `require-dev` such as `php_codesniffer` or `phpunit` will not be pushed to Pantheon by the CI scripts.

## Behat tests

So that CircleCI will have some test to run, this repository includes a configuration of [WordHat](https://wordhat.info/), A WordPress Behat extension. You can add your own `.feature` files within `/tests/behat/features`. [A fuller guide on WordPress testing with Behat is forthcoming.](https://github.com/pantheon-systems/documentation/issues/2469)

## Working locally with Lando
To get started using Lando to develop locally complete these one-time steps. Please note than Lando is an independent product and is not supported by Pantheon. For further assistance please refer to the [Lando documentation](https://docs.devwithlando.io/).

* [Install Lando](https://docs.devwithlando.io/installation/installing.html), if not already installed.
* Clone this repository locally.
* Run `lando init` and follow the prompts, choosing the Pantheon recipe followed by entering a valid machine token and selecting the Pantheon site created by [the Terminus build tools plugin].(https://github.com/pantheon-systems/terminus-build-tools-plugin).
* Run `lando start` to start Lando.
    - Save the local site URL. It should be similar to `https://<PROJECT_NAME>.lndo.site`.
* Run `lando composer install --no-ansi --no-interaction --optimize-autoloader --no-progress` to download dependencies
* Run `lando pull --code=none` to download the media files and database from Pantheon.
* Visit the local site URL saved from above.

You should now be able to edit your site locally. The steps above do not need to be completed on subsequent starts. You can stop Lando with `lando stop` and start it again with `lando start`.

**Warning:** do NOT push/pull code between Lando and Pantheon directly. All code should be pushed to GitHub and deployed to Pantheon through a continuous integration service, such as CircleCI.

Composer, Terminus and wp-cli commands should be run in Lando rather than on the host machine. This is done by prefixing the desired command with `lando`. For example, after a change to `composer.json` run `lando composer update` rather than `composer update`.

## <a name="workflow">Jenkins Workflow</a>

We're not using behat for testing - I couldn't get that to work. Check out the /features
directory for cucumber tests.

bundle exec cucumber SITE=ci-jenkins-pantheon-wordpress.pantheonsite.io STAGE=dev HTTPS=1 HEADLESS=1

When a pull request is put into GitHub, the Jenkins job kicks off, creates a Pantheon multi-dev with the new PR code, runs the cucumber tests against it. If they succeed, the multidev is deleted, but the code is saved in a git branch by Pantheon. When the pull request in merged in GitHub (now a manual process), another Jenkins job gets kicked off, and if THAT one succeeds, Pantheon merges the changes for the multidev into dev. At this point, you can work with the dev branch as you normally would (Lando etc.).

Also, each multi-dev is created based on the current dev branch, so your dev code and database updates are included.

Notes about the Pull Requests:

* They have to be pull requests against the master branch
* If you use GitFlow, push your 'release' branches to GitHub to form the pull requests
* If you don't use GitFlow, make a local branch based on the master branch, do your work there, and push that to GitHub and make that branch a pull request
* Once the pull request passes all it's tests, you have to manually merge it into master (not like the automatic merge in blacklight). After you've merged it, it will take a few minutes before the Jenkins job kicks off, and a few minutes after that before the new code is available in the Pantheon dev branch.

The Jenkins job is
https://jenkins.library.cornell.edu/view/wordpress/job/ci-jenkins-pantheon-wordpress/

The multidev sites in Pantheon are named ci-1, ci-2, ci-3, etc.

Creating a new site with this upstream:
* Create site in the organization 'Cornell University: Cornell Information Technologies'
* Use the upstream called CI-Jenkins-Wordpress-Upstream
* Once at the site dashboard, select 'install later'
* Switch the site to git development mode
* clone the code to your local machine
* go into the local copy directory
* set up the directory for git (SourceTree or whatever)
* edit the .gitignore to delete evereything from

```
# Ignore directories generated by Composer
```
to just above

```
# Add directories containing build assets below.
```
* run composer install
* git add -A
* git commit -m 'inital setup'
* git push
* set up the email secrets using terminus secrets (see [Email configuration](#email))
* go back to the site dashboard on Pantheon
* put the site in SMTP development mode (so plugins can create directories/files when they are activated)
* go to <dev site url>/wp/wp-admin (Visit Development Site)
* install wordpress
* log in to wordpress
* activate all your plugins
    * [configure SMTP email](#email)
* select your theme
* back in Pantheon, commit any changes made during installation using the SMTP Development Mode commit function.

## <a name="email">Email configuration</a>

* Install terminus secrets: https://github.com/pantheon-systems/terminus-secrets-plugin
* Set a secret for each site (dev,test,live) with the key 'SMTP_PW' and the correct value (ask for it)

```
terminus secrets:set [site].[dev,test,live] SMTP_PW [redacted]
```
* Activate the plugin 'WP Mail SMTP'
* In the settings for WP Mail SMTP:
    * From Email: libsys-mailer@cornell.edu
    * Force From Email: checked
    * From Name: Cornell University Library Web Site
    * Force From Name: checked
    * Mailer: Other SMTP
    * Return Path: checked
    * SMTP Host: email-smtp.us-east-1.amazonaws.com
    * Encryption: TLS
    * SMTP Port: 587
    * Authentication: On
    * SMTP Username: AKIAINBXRW2V76XX56BQ
    * SMTP Password: [should be set already from the terminus secret]
* Click on Save Settings
* Go to the Email Test tab
* Add your email address to the Send To field and click Send Email
* You should get an email

## <a name="new_upstream">Build a new upstream</a>

* Update the WordPress version in composer.json (if there is a new version)
* Update the regular plugin versions in composer.json
    * These are listed in the "require" section of composer.json, excluding the ones with a 'cul-it/' prefix in the path.
    * The Pantheon site wp-ci-library-cornell-edu can be used as a reference. It has all the upstream plugins enabled (except Akismet Anti-Spam which requires additional configuration).
    * Do not update the plugin verson unless the Compatablilty with the new WordPress version is 100%.
* Update the CULU theme.
    * Use Git to pull a new local copy of the Pantheon site culu-library-cornell-edu
    * The GitHub repo for the theme is https://github.com/cul-it/library-cornell-edu-theme - this corresponds to the theme in site culu-library-cornell-edu. Pull the latest local copy of this.
    * Migrate the changes from culu-library-cornell-edu/wp-content/themes/culu into library-cornell-edu-theme, being sure not to disturb .git and composer.json in library-cornell-edu-theme
    * Commit the changes to library-cornell-edu-theme
    * Tag the change with the next version number (e.g. v1.0.14) in library-cornell-edu-theme in GitHub
    * Update the version number in composer.json (e.g. "cul-it/culu": "1.0.14",)
* Update the cul-it plugin repos
    * These are listed in the "require" section of composer.json, with a 'cul-it/' prefix in the path.
    * These are the 'Pro' or 'Paid' versions of WordPress plugins we've purchased.
    * Each one has a GitHub repo containing the pro version of the plugin.
    * The repos must be listed in the "repositories" section of composer.json
    * Each repo must contain a composer.json file
        * Example:
          ```
          {
              "name": "cul-it/advanced-custom-fields-pro",
              "description": "WordPress plugin for Unified Library Website",
              "type": "wordpress-plugin",
              "minimum-stability": "dev",
              "prefer-stable": true,
              "require": {
                  "composer/installers": "^1.0"
              }
          }
          ```
    * Download and merge the latest version of the plugin from the vendor's web site
    * Tag it with the proper version number in the GitHub repo (If you need another version of the same source version, you can add a .1 after the plugin's version.)
    * Match the version number in the "require" section of composer.json
* After all the updates to composer.json in ci-jenkins-pantheon-wordpress, run composer update in the local version to update composer.lock
* Make a release tagged with the next version number for ci-jenkins-pantheon-wordpress (e.g. "release/v1.2.11")
* Push the release to GitHub and submit a Pull Request
* Jenkins should run and complete the tests: https://jenkins.library.cornell.edu/job/ci-jenkins-pantheon-wordpress/
* If all goes well, finish the release, merge it into master, and push master to GitHub.
* Jenkins should run and complete the tests a second time, this time creating a new version of the upstream in Pantheon
* Test the new upstream
    * Go to the Pantheon site wp-ci-library-cornell-edu and check for upstream updates
    * Apply the updates
    * Clone wp-ci-library-cornell-edu to the local machine
    * Run composer install
    * Commit to master and push to Pantheon
    * Go to site administration in Pantheon and activate any new plugins (except Akismet Anti-Spam)
