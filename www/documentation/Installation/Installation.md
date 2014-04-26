For Developers
--------------
This documentation is only for developers. 

Checkout the git project
------------------------
To get the code execute a git clone:
WRITE A GIT CLONE COMMAND

Run composer
------------
To download DEPENDENCIES run

Configuration ichnaea WebApp
----------------------------
In www/app/parameters.yml the parameters for the instance, you can configure databases, mailers, FS. The folder has a several files for enviroments. If the is the first time, copy the www/app/config/config-dist.yml into www/app/config/config.yml. Git is configured for ignore this specific files.


Insert some basic dato to start operate
---------------------------------------
a) Update the database schema
app/console doctrine:schema:update --dump-sql
app/console doctrine:schema:update --force

b) First execute to have a user into the web app with admin
php app/console doctrine:fixtures:load

c) Extra: Needs to configure the matrix from the beginnig

