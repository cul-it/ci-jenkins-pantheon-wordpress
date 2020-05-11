#!/bin/bash
# From https://github.com/tburry/travis-nginx-test

# Exit if anything fails AND echo each command before executing
# http://www.peterbe.com/plog/set-ex
set -ex

USER=$(whoami)
PHP_VERSION=$(phpenv version-name)
ROOT=$WH_WORDPRESS_DIR
PORT=9000
SERVER=wordpress.dev

function tpl {
  sed \
    -e "s|{DIR}|$WH_NGINX_DIR|g" \
    -e "s|{USER}|$USER|g" \
    -e "s|{PHP_VERSION}|$PHP_VERSION|g" \
    -e "s|{ROOT}|$ROOT|g" \
    -e "s|{PORT}|$PORT|g" \
    -e "s|{SERVER}|$SERVER|g" \
    < $1 > $2
}

mkdir -p "$WH_NGINX_DIR/nginx/sites-enabled"

# Configure the PHP handler.
PHP_FPM_BIN="$HOME/.phpenv/versions/$PHP_VERSION/sbin/php-fpm"
PHP_FPM_CONF="$WH_NGINX_DIR/nginx/php-fpm.conf"

# Start php-fpm.
tpl "$WH_DIR/bin/travis/php-fpm.tpl.conf" "$PHP_FPM_CONF"
"$PHP_FPM_BIN" --fpm-config "$PHP_FPM_CONF"

# Build the default nginx config files.
tpl "$WH_DIR/bin/travis/nginx.tpl.conf" "$WH_NGINX_DIR/nginx/nginx.conf"
tpl "$WH_DIR/bin/travis/fastcgi.tpl.conf" "$WH_NGINX_DIR/nginx/fastcgi.conf"
tpl "$WH_DIR/bin/travis/default-site.tpl.conf" "$WH_NGINX_DIR/nginx/sites-enabled/default-site.conf"

# Start nginx.
nginx -c "$WH_NGINX_DIR/nginx/nginx.conf"
