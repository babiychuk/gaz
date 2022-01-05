#!/bin/bash
PROOT=$(git rev-parse --show-toplevel)
cd $PROOT

set -e

echo "git pull"
git pull

echo "composer install"
composer install

cd web
echo "drush updb"
drush updb

echo "drush config-import -y"
drush config-import -y

echo "drush fa-download"
drush fa-download

cd themes/uag
echo "npm install"
npm install

echo "./node_modules/.bin/gulp build"
./node_modules/.bin/gulp build

cd "$PROOT/web"
echo "drush cr"
drush cr
