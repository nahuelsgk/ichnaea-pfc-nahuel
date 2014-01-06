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
Option a) 
If u want some initial data as variables use this option.
a) Insert some initial data located on DefaultData importing it.
b) Append some initial fixtures executring from root symfony folder. This will append an admin user and group users, and enroll all users created as a normal user
php app/console doctrine:fixtures:load --append

Option b) 
No data available. Execute symfony command to create the database and execute the Option a) -> b) step


Some things you should know
---------------------------
[] Integration with rabbitmq: /opt/lampp/htdocs/ichnaea.alt/amqp/php/vendor/composer/autoload_real.php 
Had to comment, beacause doctrine gave conflict when persist a training.


Mail configuration
------------------

