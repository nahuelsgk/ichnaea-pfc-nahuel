- To access to the server
ssh USERNAME@login1.lsi.upc.edu 
ssh2 ichnaea@ichnaea with password ihp0cn3s

- Deploy a new version
In /home/ichnaea/ichnaea/ichnaea, execute :
'git pull origin master'
To update dependencies(we need to ask access to systems to deploy it):
'php composer.phar install'
Otherwise to update dependencies 'php composer.phar update'
For install some dependencies, we need to ask rdlab@lsi.upc.edu to open the firewall.

- Read the Installation guide: "Insert some basic dato to start operate" in www/documentation/Installation/Installation.md

- Deploy amqp dependencies project no-dev from /home/ichnaea/ichnaea/ichnaea/amqp/php
'php composer.phar install'

- Clear the app cache
"php app/console cache:clear"

- Access to the web

