#!/bin/bash
cd $(git rev-parse --show-toplevel)
cd web

drush sql-sync @stage @self -y
drush rsync @stage:%files @self:%files -y
