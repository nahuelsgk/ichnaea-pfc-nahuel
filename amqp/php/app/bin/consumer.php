<?php
require_once __DIR__.'/../../vendor/autoload.php';

use \Doctrine\DBAL\Configuration as DbConfiguration;
use \Doctrine\DBAL\DriverManager as DbDriverManader;

use \Ichnaea\Amqp\Connection as AmqpConnection;
use \Ichnaea\Amqp\Model\BuildModelsResponse;

// define('AMQP_DEBUG', true);

$dbConfig = new DbConfiguration();
$dbParams = array(
    'driver'   => 'pdo_sqlite',
    'path'     => __DIR__.'/../app.db',
);
$db = DbDriverManader::getConnection($dbParams, $dbConfig);

$amqp = new AmqpConnection("test:test@localhost");
$amqp->open();

$amqp->listenForBuildModelResponse(function(BuildModelsResponse $resp) use ($db) {
	print "Received build-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
	$data = $resp->toArray();
	if (!$db->update('build_models_tasks', $data, array('id'=>$resp->getId()))) {
		$db->insert('build_models_tasks', $data);
	}
});

$amqp->wait();