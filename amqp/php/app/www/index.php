<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../config.php';

use Symfony\Component\HttpFoundation\Request;

use Ichnaea\Amqp\Connection;
use Ichnaea\Amqp\Model\BuildModelsRequest;
use Ichnaea\Amqp\Model\BuildModelsResponse;
use Ichnaea\Amqp\Model\PredictModelsRequest;
use Ichnaea\Amqp\Model\PredictModelsResponse;
use Ichnaea\Amqp\Model\FakeRequest;
use Ichnaea\Amqp\Model\FakeResponse;
use Ichnaea\Amqp\Exception\IchnaeaExceptionInterface;


// setup timezone to prevent error when using \DateTime
date_default_timezone_set(@date_default_timezone_get());

function getView($name) {
    return file_get_contents(__DIR__.'/../views/'.$name);
}

$app = new Silex\Application();
$app['debug'] = true;
$app['ichnaea_amqp'] = new Connection(ICHNAEA_AMQP_URL);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../db/app.db',
    ),
));

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->before(function (Request $request) use ($app) {
    $app['ichnaea_amqp']->open();
});

$app->error(function (\Exception $e, $code) {
    if($e instanceof IchnaeaExceptionInterface) {
        $app['ichnaea_amqp'] = null;
    }
});

$app->after(function () use ($app) {
    if(isset($app['ichnaea-amqp'])) {
        $app['ichnaea_amqp']->close();
    }
});

$app->get('/', function () use ($app) {
    return getView("home.html");
});

$app->get('/tasks', function (Request $req) use ($app) {
    $sql = "SELECT * FROM tasks";
    $data = $app['db']->fetchAll($sql);
    return json_encode(array('tasks'=>$data));
});

$app->post('/tasks', function (Request $req) use ($app) {
    $data = $req->request->get("task");
    if(is_string(($data))) {
        $data = json_decode($data);
    }
    $type = '';
    if(array_key_exists('type', $data)) {
        $type = $data['type'];
    }

    if($type == 'build-models') {
        if(!array_key_exists('aging_positions', $data)) {
            $data['aging_positions'] = array(
                'Estiu'     => '0.5',
                'Hivern'    => '0.0',
                'Summer'    => '0.5',
                'Winter'    => '0.0'
            );
        }
        $model = BuildModelsRequest::fromArray($data);
        $app['ichnaea_amqp']->send($model);
        $model = new BuildModelsResponse($model->getId());
        $data = $model->toArray();
    } else if($type == 'predict-models') {
        if(array_key_exists('data', $data)) {
            $data['data'] = base64_decode($data['data']);
        }
        $model = PredictModelsRequest::fromArray($data);
        $app['ichnaea_amqp']->send($model);
        $model = new PredictModelsResponse($model->getId());
        $data = $model->toArray();
    } else if($type == 'fake') {
        $model = FakeRequest::fromArray($data);
        $app['ichnaea_amqp']->send($model);
        $model = new FakeResponse($model->getId());
        $data = $model->toArray();
    } else {
        return json_encode(array());
    }

    $data['type'] = $type;
    $app['db']->insert('tasks', $data);
    return json_encode(array('task'=>$data));
});

$app->get('/tasks/{id}', function ($id) use ($app) {
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $data = $app['db']->fetchAssoc($sql, array($id));
    return json_encode(array('task'=>$data));
});

$app->delete('/tasks/{id}', function ($id) use ($app) {
    $app['db']->delete('tasks', array('id' => $id));
    return json_encode(array());
});

$app->run();
