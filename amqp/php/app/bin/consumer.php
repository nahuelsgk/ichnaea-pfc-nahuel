<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use \Doctrine\DBAL\Configuration as DbConfiguration;
use \Doctrine\DBAL\DriverManager as DbDriverManader;

use \Ichnaea\Amqp\Connection as AmqpConnection;
use \Ichnaea\Amqp\Model\BuildModelsResponse;
use \Ichnaea\Amqp\Model\PredictModelsResponse;
use \Ichnaea\Amqp\Model\FakeResponse;

// define('AMQP_DEBUG', true);

// setup timezone to prevent error when using \DateTime
date_default_timezone_set(@date_default_timezone_get());

$dbConfig = new DbConfiguration();
$dbParams = array(
    'driver'   => 'pdo_sqlite',
    'path'     => __DIR__.'/../db/app.db',
);
$db = DbDriverManader::getConnection($dbParams, $dbConfig);

$amqp = new AmqpConnection(ICHNAEA_AMQP_URL);

$amqp->open();

function updateTask($type, array $data) {
    global $db;
	$data['type'] = $type;
    if(array_key_exists('id', $data)) {
        $db->update('tasks', $data, array('id'=> $data['id']));
    }
}

print "listening to build-models responses...\n";
$amqp->listenForBuildModelResponse(function(BuildModelsResponse $resp) use ($db) {
    print "Received build-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
    updateTask('build-models', $resp->toArray());
});

print "listening to predict-models responses...\n";
$amqp->listenForPredictModelsResponse(function(PredictModelsResponse $resp) use ($db) {
    print "Received predict-models response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
    updateTask('predict-models', $resp->toArray());
});

print "listening to fake responses...\n";
$amqp->listenForFakeResponse(function(FakeResponse $resp) use ($db) {
    print "Received fake response ".$resp->getId()." ".intval($resp->getProgress()*100)."%\n";
    updateTask('fake', $resp->toArray());
});

$amqp->wait();
