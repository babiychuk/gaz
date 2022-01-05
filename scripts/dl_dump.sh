#!/usr/bin/env bash
set -e
cd $(git rev-parse --show-toplevel)
#FOLDER="1fQC8mSj94s9ui8mtP4nl9PGUMlAV0GK8"
ID="1hrWzdzoLbJ8V_h3ybEXjE3WWEGFzICgX"
URL="https://drive.google.com/uc?id=$ID"
echo "$URL"
wget -O sql/modx_latest.sql "$URL"

echo "To setup modx db run command(modified):"
echo "mysql --host=localhost --user=drupal -p old <\"sql/modx_latest.sql\""

echo "Actual settings:"
cd web && drush sql-connect --database=migrate
