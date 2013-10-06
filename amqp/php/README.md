Setting up Ichnaea Amqp with php
================================

Setup
-----

### Install composer

Composer is a tool to manage php dependencies.

    curl -sS https://getcomposer.org/installer | php

This command will install the dependent libraries

    php composer.phar install --no-dev

### Create a RabbitMQ user

The Java library does not need a rabbitmq user, but the php does.
TO create a test user with all the permissions do the following

    rabbitmqctl add_user test test
    rabbitmqctl add_vhost /
    rabbitmqctl set_permissions -p / test ".*" ".*" ".*" 

Usage
-----

### Sending a `BuildModelsRequest`

    use Ichnaea\Amqp\Connection;
	use Ichnaea\Amqp\Model\BuildModelsRequest;

	$conn = new Connection("test:test@localhost");
	$conn->open();
	$req = BuildModelsRequest::fromArray(array(
		'id'		=> 'test'
		'dataset'	=> "csv_data"
	));
	$conn->send($req);
	$conn->close();

### Listening for `BuildModelsResponse`

Listening has always to be done in a separate process as it will block execution.

	$conn = new AmqpConnection("test:test@localhost");
	$conn->open();

	$amqp->listenForBuildModelResponse(function(BuildModelsResponse $resp) use ($db) {
		print "Received build-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
		$data = $resp->toArray();
		// save data
	});

	$amqp->wait();


Test app
--------

In the `app` folder there is a test app, to install it do the following.

* Install app dependencies

    php composer.phar install

* Build database

    php php/app/bin/build_database.php

* Change config.php to point to a running amqp server

* Configure the webserver to point to `php/app/www`

* Run the java ichnaea request process consumer

    mvn exec:java -Dexec.args="build-models:process -i ../kvm/files/ichnaea.sh"

* Run the php response process consumer

	php php/app/bin/consumer.php