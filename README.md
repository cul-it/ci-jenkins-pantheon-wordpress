# ci-jenkins-pantheon-wordpress

This repository is a customized Composer-based WordPress workflow for Pantheon. It is currently used to manage and push updates to CUL Wordpress environments on Pantheon.

The workflow is based off of [Example Wordpress Composer](https://github.com/pantheon-systems/example-wordpress-composer). For more background information on this style of workflow, see the [Pantheon documentation](https://pantheon.io/docs/guides/github-pull-requests/).

**skip to [Jenkins Workflow](#workflow)**
**skip to [Create a new site with upstream](#create-new-site-with-upstream)**
**skip to [Build a new upstream](#build-new-upstream)**

## Installation

Note the documentation below is for [Example Wordpress Composer](https://github.com/pantheon-systems/example-wordpress-composer) and will need to adapted before executing. 

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

## Tests

This repository includes a configuration of [WordHat](https://wordhat.info/), A WordPress Behat extension. You can add your own `.feature` files within `/tests/behat/features`. [A fuller guide on WordPress testing with Behat is forthcoming.](https://github.com/pantheon-systems/documentation/issues/2469)

However, Behat tests are not currently in use, as past CUL-IT teams could not get them to run with this workflow. Instead, see the /features directory for cucumber tests.

bundle exec cucumber SITE=ci-jenkins-pantheon-wordpress.pantheonsite.io STAGE=dev HTTPS=1 HEADLESS=1

## <a name="workflow">Jenkins Workflow</a>

When a pull request is opened in GitHub, the Jenkins job kicks off. This creates a Pantheon multidev environment with the new PR code and runs the cucumber tests against it. If these tests succeed, the multidev is deleted, but the code is saved in a git branch by Pantheon.

When the pull request in merged in GitHub (now a manual process), an additional Jenkins job kicks off, and if it succeeds, Pantheon merges the changes for the multidev into dev. At this point, you can work with the dev branch as you normally would (including with Lando). 

### Notes about the Pull Requests:

* If you use gitflow, push your 'release' branches to GitHub to form the pull requests. 
* If you don't use gitflow, make a local branch based on the master branch, do your work there, and push that to GitHub and make that branch a pull request. 
* Once the pull request passes all its tests, you have to manually merge it into master. After you've merged it, it will take a few minutes before the Jenkins job kicks off and a few minutes after that before the new code is available in the Pantheon dev branch.

The Jenkins job is
https://awsjenkins.library.cornell.edu/job/ci-jenkins-pantheon-wordpress/

The multidev sites in Pantheon are named ci-1, ci-2, ci-3, etc.

## <a name="create-new-site-with-upstream">Create a new site with upstream</a>

Creating a new site with this upstream:
* Create Pantheon site in the organization 'Cornell University: Cornell Information Technologies'
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

### <a name="email">Email configuration</a>

* Install terminus secrets: https://github.com/pantheon-systems/terminus-secrets-plugin
* Set a secret for each site (dev,test,live) with the key 'SMTP_PW' and the correct value (ask for it)
* Set secrets 'SAML_SALT' and 'SAML_ADMIN_PAW' for each site

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

### Check for updates 

1. Use the upstream test site in Pantheon, uls-upstream-test, to check for updates
   * Make sure the site environments are up-to-date with the latest upstream before proceeding

2. Check plugin updates, core WordPress updates, and core theme updates to determine what should be added to the new upstream
   * Option 1: Go to the Patheon admin dashboard for uls-upsteam-test's Dev environment and select the "Status" menu option and then "run the checks now." This will give you a list of the plugin packages that need updating.
   * Option 2: Check Wordpress admin UI -- see step #3 for more details.

3. Check the plugins' compatibility with the WordPress version installed in the Pantheon instances (or the version about to be installed)
   * Go to admin dashboard for uls-upsteam-test's multidev environments and select "getupstream" option and then "Visit getupstream site." 
   * Login and navigate to "Updates" page in Wordpress admin UI. This will show you the list of plugins available to update and the expected compatiability with WP versions. 

### Update composer files

4. Clone ci-jenkins-pantheon-wordpress locally and open a new branch
    * In the past, this project has often used gitflow with develop, feature, and release branches. 
    * If you don't use gitflow, make a local branch based on the master branch and do your work there. You may want to use the naming convention for gitflow release branches using the next version number for ci-jenkins-pantheon-wordpress (e.g. "release/v1.2.11"). 
5. Open and edit composer.json
    * Update the regular plugin versions
        * These are listed in the "require" section of composer.json, excluding the ones with a 'cul-it/' prefix in the path.
        * The Pantheon site wp-ci-library-cornell-edu can be used as a reference. It has all the upstream plugins enabled (except Akismet Anti-Spam which requires additional configuration).
        * Do not update the plugin verson unless the Compatablilty with the new WordPress version is 100%.
    * Update the WordPress version, if appliable
        * Find the plugin pantheon-systems/wordpress-composer and update with the new WordPress version number
    * Update the cul-it plugin repositories, if applicable
        * These plugins are typically the 'Pro' or 'Paid' versions of WordPress plugins that we've purchased.
        * Each will be listed in the "require" section of composer.json, with a 'cul-it/' prefix in the path. 
        * Each will have their own corresponding private CUL-IT Github repository. These repositories must be listed in the "repositories" section of composer.json.
        * Download the latest version of the plugin from the vendor's web site and merge to the plugin's CUL-It GitHub repository. Tag it with the proper version number in the GitHub repository. If you need another version of the same source version, you can add a .1 after the plugin's version.
        * Update the version number in the "require" section of composer.json to match. 
    * Update the CULU theme, if applicable
        * Tag the new release with the next version number (e.g. `v1.3.0`) in [cul-it/wp-cul-theme-culu](https://github.com/cul-it/wp-cul-theme-culu)
        * Update the version number in the "require" section of composer.json to match

6. Run in terminal: 
    ```
    rm composer.lock
    ```

    ```
    composer install
    ```

   If you get this error:
   ```
   Fatal error: Allowed memory size of 1610612736 bytes exhausted (tried to allocate 4096 bytes) in phar:///usr/local/bin/composer.phar/src/Composer/DependencyResolver/RuleWatchGraph.php on line 52
   ```
   Type, `export COMPOSER_MEMORY_LIMIT=-1`, for fixing the error and then run again, `composer install`

7. Open and edit `changelog.md` to document updates

### Test and finalize updates

8. Commit changes, push to GitHub, and open a pull request
    * If you are using gitflow, make a release branch tagged with the next version number for ci-jenkins-pantheon-wordpress (e.g. "release/v1.2.11") before pushing to GitHub
    * This will kick off a Jenkins job and run tests. See [Jenkins Workflow](#workflow) for details. 

9. If the tests pass, merge the pull request to master
    * If you did not use gitflow, create a release in GitHub and tag the release with the next version number for ci-jenkins-pantheon-wordpress (e.g. "release/v1.2.11"). 
    * This will kick off a second Jenkins job and create a new version of the upstream in Pantheon. See [Jenkins Workflow](#workflow) for details. 

10. Test the new upstream
    * Go to the Pantheon site wp-ci-library-cornell-edu and check for upstream updates
    * Apply the updates
    * Clone wp-ci-library-cornell-edu to the local machine
    * Run `composer install`
    * (SEE STEP 11, if applicatble, run `bash saml-link-add.sh`)
    * Commit to master and push to Pantheon
    * Go to site administration in Pantheon and activate any new plugins (except Akismet Anti-Spam)

11. Updating simplesamlphp library: if simplesamlphp was updated, the symlinks will have been deleted and the shiboleth one-click login functionality will be broken in production for the unit sites. To recreate the symlinks to the private dir you need to take the extra step of running the bash script saml-link-add. This can not be tested on the uls-upstream-test site, only the live environment for unit sites with domain names.

When ready, apply upstream to indvidual Wordpress sites in Pantheon. 

## Working locally with Lando

Lando can be used for local development and testing. 

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