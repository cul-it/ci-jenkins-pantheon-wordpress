#!/bin/bash

# remove all the plugins that are installed by the upstream.
# this is for the cases where someone intalled them via WordPress SFTP
# that kind of install totally fouls up the composer autoload

set -e

BASEDIR=$(dirname "$0")
cd "$BASEDIR/../../"
if [ -d "./vendor" ] && [ -d "./web/wp-content/plugins" ]
then
    cd "./web/wp-content/plugins"
else
    echo "Please run $0 from the site root"
    exit 1
fi

PLUGINS=(
        "advanced-custom-fields-pro"
        "ares_wordpress"
        "cul-saml-auth"
        "draw-attention-pro"
        "elementor-pro"
        "facetwp"
        "facetwp-conditional-logic"
        "wp-custom-loop-widget"
        "wp-libcal-hours"
        "wp-rss-aggregator"
        "wp-rss-categories"
        "wp-rss-keyword-filtering"
        "wp-rss-templates"
        "wp-ultimate-csv-importer-pro"
        "acf-better-search"
        "acf-image-aspect-ratio-crop"
        "acf-to-rest-api"
        "advanced-access-manager"
        "akismet"
        "anywhere-elementor"
        "better-font-awesome"
        "capability-manager-enhanced"
        "code-snippets"
        "coming-soon"
        "custom-icons-for-elementor"
        "custom-post-type-ui"
        "easy-notification-bar"
        "elementor"
        "filebird"
        "google-analytics-dashboard-for-wp"
        "granular-controls-for-elementor"
        "intuitive-custom-post-order"
        "json-content-importer"
        "kirki"
        "pantheon-advanced-page-cache"
        "redirection"
        "relevanssi"
        "simple-social-icons"
        "siteimprove"
        "wp-cfm"
        "wp-mail-smtp"
        "wp-native-php-sessions"
        "wp-rss-aggregator"
)

for PLUG in "${PLUGINS[@]}"
do
    echo "Removing $PLUG"
    rm -rf "$PLUG"
done