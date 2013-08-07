<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use Symfony\Component\HttpFoundation\Request;

use Ichnaea\Amqp\Connection;
use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse;

function getView($name)
{
    return file_get_contents(__DIR__.'/../views/'.$name);
}

$app = new Silex\Application();
$app['debug'] = true;
$app['ichnaea_amqp'] = new Connection(ICHNAEA_AMQP_URL);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../app.db',
    ),
));

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->before(function () use ($app) {
    $app['ichnaea_amqp']->open();
});

$app->after(function () use ($app) {
    $app['ichnaea_amqp']->close();
});

$app->get('/', function () use ($app) {
    return getView("home.html");
});

$app->get('/build-models-tasks', function (Request $req) use ($app) {
    $sql = "SELECT * FROM build_models_tasks";
    $data = $app['db']->fetchAll($sql);
    foreach ($data as $k=>$v) {
        $data[$k] = BuildModelsResponse::fromArray($v)->toArray();
    }

    return json_encode(array('build-models-tasks'=>$data));
});

$app->post('/build-models-tasks', function (Request $req) use ($app) {
    $data = $req->request->get("build-models-task");
    $model = BuildModelsRequest::fromArray($data);
    $app['ichnaea_amqp']->send($model);
    $model = new BuildModelsResponse($model->getId());
    $app['db']->insert('build_models_tasks', $model->toArray());

    return json_encode(array('build-models-task'=>$model->toArray()));
});

$app->get('/build-models-tasks/{id}', function ($id) use ($app) {
    $sql = "SELECT * FROM build_models_tasks WHERE id = ?";
    $data = $app['db']->fetchAssoc($sql, array($id));
    $data = BuildModelsResponse::fromArray($data)->toArray();

    return json_encode(array('build-models-task'=>$data));
});

$app->delete('/build-models-tasks/{id}', function ($id) use ($app) {
    $app['db']->delete('build_models_tasks', array('id' => $id));

    return json_encode(array());
});

$app->run();
