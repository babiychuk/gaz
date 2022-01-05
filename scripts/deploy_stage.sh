#!/bin/bash
set -e

echo "Working from user $USER"

PROOT='/home/gaz/www/stage'
PTHEME='web/themes/uag'
BRANCH='master'

echo ""
echo "Switching to project docroot.: $PROOT"
cd "$PROOT"
echo ""
echo "Pulling down the latest code from $BRANCH branch."
git pull origin $BRANCH
echo "Running composer install"
composer install

echo ""
cd web
echo "Clearing drush caches."
drush cache-clear drush
echo ""
echo "Running database updates."
drush updb -y
echo ""
echo "Importing configuration."
drush config-import -y

echo ""
echo "Building theme assets"
cd "$PROOT/$PTHEME"

npm install
./node_modules/.bin/gulp build

echo "Download font-awesome library"
drush fa-download

cd "$PROOT/web/"

echo ""
echo "Clearing caches."
drush cr
echo ""
echo "Deployment complete."
