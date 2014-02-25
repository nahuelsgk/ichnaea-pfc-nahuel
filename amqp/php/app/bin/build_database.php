<?php
require_once __DIR__.'/../../vendor/autoload.php';

use \Doctrine\DBAL\Configuration;
use \Doctrine\DBAL\DriverManager;
use \Doctrine\DBAL\Schema\Table;

$config = new Configuration();

$connectionParams = array(
    'driver'   => 'pdo_sqlite',
    'path'     => __DIR__.'/../db/app.db',
);
$conn = DriverManager::getConnection($connectionParams, $config);
$sm = $conn->getSchemaManager();

$table = new Table("tasks");
$table->addColumn("id", "string");
$table->addColumn("type", "string");
$table->addColumn("start", "string");
$table->addColumn("end", "string", array('notnull'=>false));
$table->addColumn("progress", "float");
$table->addColumn("error", "text", array('notnull'=>false));
$table->addColumn("data", "blob", array('notnull'=>false));
$table->setPrimaryKey(array("id"));
$sm->dropAndCreateTable($table);

$conn->insert('tasks', array(
    'id' 		=> 'test',
    'type'		=> 'fake',
    'start'		=> '01/04/2013 22:00',
    'end'		=> '02/04/2013 16:00',
    'progress'	=> 0.5,
    'error'		=> 'Test request'
));
