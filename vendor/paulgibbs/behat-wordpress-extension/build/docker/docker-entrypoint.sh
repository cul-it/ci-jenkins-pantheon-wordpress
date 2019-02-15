#!/bin/bash
set -eux

WH_WORDPRESS_DIR=/usr/src/wordpress

# wait for mysql to come up
while ! mysqladmin ping -h "db"; do
    echo "Waiting for mysql..."
    sleep 1
done

# now wait for selenium (assume if the port is open then selenium is up)

until nc -z -v -w30 selenium 4444
do
  echo "Waiting for selenium..."
  sleep 1
done

vendor/bin/wp config create --path="${WH_WORDPRESS_DIR}" --dbhost='db' --dbname='wordpress' --dbuser="${WORDPRESS_DB_USER}" --dbpass="${WORDPRESS_DB_PASSWORD}"
vendor/bin/wp core install --path="${WH_WORDPRESS_DIR}" --url='http://wordpress:8080/' --title="Wordhat Wordpress Install" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com"
vendor/bin/wp theme activate --path="${WH_WORDPRESS_DIR}" twentyseventeen
vendor/bin/wp rewrite structure --path="${WH_WORDPRESS_DIR}" '/%year%/%monthnum%/%postname%/'
mkdir -p ${WH_WORDPRESS_DIR}/wp-content/mu-plugins && curl -o ${WH_WORDPRESS_DIR}/wp-content/mu-plugins/disable-gutenberg.php https://gist.githubusercontent.com/paulgibbs/6d6309e0ea586d955e0b7b5573d5a642/raw/f8961ab10b818379c209359b36d9ad0d4ed9bbde/disable-gutenberg.php
cd /wordhat

# The default widgets often repeat post titles and confuse Behat.
for sidebar in $(vendor/bin/wp sidebar list --path="${WH_WORDPRESS_DIR}" --format=ids); do
  for widget in $(vendor/bin/wp widget list $sidebar --path="${WH_WORDPRESS_DIR}" --format=ids); do
    vendor/bin/wp widget delete --path="${WH_WORDPRESS_DIR}" $widget
  done;
done;

cd /var/www/html
exec /usr/local/bin/docker-entrypoint.sh apache2-foreground
