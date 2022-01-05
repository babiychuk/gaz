# ukravtonomgaz.ua site repository

## Local env
First, you need:   

 * virtualbox   
 * vagrant   
 * git   
 * composer   
Also you need configured ssh keys on remote environment (prod/stage).    

    
Then run commands:
    
1. git clone   
1. composer install   
1. create `config/local.config.yml` (see example file)   
1. vagrant up   

Then you can go to [http://dashboard.gaz.local]   
And ssh to your host via `vagrant ssh`   
   
Then you need to setup site. Run next commands from vagrant (ssh):   

1. (optionally) create `drush/local.drush.yml`   
1. Place config in `web/sites/default/settings.php`    
1. `drush sql-sync @prod @self`   
1. `drush rsync @prod:%files @self:%files`    
 
