For Developers
--------------

Checkout the git project
------------------------

Run composer
------------

Copy the dist file
------------------

Configure your database
-----------------------

Insert some basic dato to start operate
---------------------------------------
In the folder www/documentation/Installation/DefaultData, you can use some default data to insert directly into an EMPTY DATABASE.
a) First execute to have a user into the web app with admin
php app/console doctrine:fixtures:load --append
b) Insert the data into dabatase. The file to insert into the database: www/documentation/Installation/DefaultData/raw_fixtures.sql




Some things you should know
---------------------------
Had to comment, beacause doctrine gave conflict when persist a training.


Mail configuration
------------------

