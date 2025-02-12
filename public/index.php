<?php

session_start();

use App\Controller\CertificateController;
use App\Controller\LoginController;
use App\Controller\ReportController;
use App\Controller\ResultController;
use App\Model\ReportModel;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    [
        PDO::class => function () {
            $dsn = $_ENV['DB_DRIVER'];
            $dsn .= ':host=' . $_ENV['DB_HOST'];
            $dsn .= ';port=' . $_ENV['DB_PORT'];
            $dsn .= ';dbname=' . $_ENV['DB_NAME'];
            $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        },
        Twig::class => function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        },
    ],
);
$container = $containerBuilder->build();

$app = $app = Bridge::create($container);
$app->addRoutingMiddleware();
$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
$app->addErrorMiddleware($_ENV['DEBUG'], $_ENV['DEBUG'], $_ENV['DEBUG']);

$app->get('/', [LoginController::class, 'login']);
$app->post('/login', [LoginController::class, 'loginPost']);

$routes = ['doctor', 'nurse', 'test', 'patient'];
array_walk($routes, function ($route) use ($app) {
    $path = '/' . $route . 's';
    $controller = 'App\\Controller\\' . ucfirst($route) . 'Controller';
    $app->get($path, [$controller, 'index']);
    $app->get($path . '/create', [$controller, 'create']);
    $app->post($path . '/create', [$controller, 'createPost']);
    $app->get($path . '/{id}', [$controller, 'view']);
    $app->get($path . '/{id}/edit', [$controller, 'edit']);
    $app->post($path . '/{id}/edit', [$controller, 'editPost']);
    $app->get($path . '/{id}/delete', [$controller, 'delete']);
    $app->post($path . '/{id}/delete', [$controller, 'deletePost']);
});

$app->get('/tests/{testId}/results/create', [ResultController::class, 'create']);
$app->post('/tests/{testId}/results/create', [ResultController::class, 'createPost']);
$app->get('/tests/{testId}/results/{resultId}/edit', [ResultController::class, 'edit']);
$app->post('/tests/{testId}/results/{resultId}/edit', [ResultController::class, 'editPost']);
$app->get('/tests/{testId}/results/{resultId}/delete', [ResultController::class, 'delete']);
$app->post('/tests/{testId}/results/{resultId}/delete', [ResultController::class, 'deletePost']);

$app->get('/patients/{patientId}/certificates/create', [CertificateController::class, 'create']);
$app->post('/patients/{patientId}/certificates/create', [CertificateController::class, 'createPost']);
$app->get('/patients/{patientId}/certificates/{certificateId}/delete', [CertificateController::class, 'delete']);
$app->post(
    '/patients/{patientId}/certificates/{certificateId}/delete',
    [CertificateController::class, 'deletePost'],
);

$app->get('/patients/{patientId}/reports/create', [ReportController::class, 'create']);
$app->post('/patients/{patientId}/reports/create', [ReportController::class, 'createPost']);
$app->get('/patients/{patientId}/reports/{reportId}/delete', [ReportController::class, 'delete']);
$app->post('/patients/{patientId}/reports/{reportId}/delete', [ReportController::class, 'deletePost']);

$app->get('/x', function (ReportModel $model) {
    $model->generate();
});

$app->run();
