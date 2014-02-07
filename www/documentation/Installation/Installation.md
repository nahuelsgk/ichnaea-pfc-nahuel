For Developers
--------------

Checkout the git project
------------------------

Run composer
------------

Configuration ichnaea WebApp
----------------------------
In www/app/parameters.yml the parameters for the instance, you can configure databases, mailers. The fodler has a several files for enviroments. If the is the first time, copy the www/app/config/config-dist.yml into www/app/config/config.yml. Git is configured for ignore this specific files.


Insert some basic dato to start operate
---------------------------------------
In the folder www/documentation/Installation/DefaultData, you can use some default data to insert directly into an EMPTY DATABASE.
a) Update the database schema
app/console doctrine:schema:update --dump-sql
app/console doctrine:schema:update --force

b) First execute to have a user into the web app with admin
php app/console doctrine:fixtures:load --append

c) Extra: Insert the data into dabatase. The file to insert into the database: www/documentation/Installation/DefaultData/raw_fixtures.sql
Warning: consider to update the field owner_id of the table matrix to an existing user.
"update matrix set owner_id=1 where id=14;"

