<?php

session_start();

use App\Controller\CertificateController;
use App\Controller\LoginController;
use App\Controller\ReportController;
use App\Controller\ResultController;
use App\Middleware\AuthenticationMiddleware;
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
$app->get('/login', [LoginController::class, 'login']);
$app->post('/login', [LoginController::class, 'loginPost']);
$app->get('/logout', [LoginController::class, 'logout']);

$auth = new AuthenticationMiddleware();

$routes = ['doctor', 'nurse', 'test', 'patient'];
foreach ($routes as $route) {
    $path = '/' . $route . 's';
    $controller = 'App\\Controller\\' . ucfirst($route) . 'Controller';
    $app->get($path, [$controller, 'index'])->add($auth);
    $app->get($path . '/create', [$controller, 'create'])->add($auth);
    $app->post($path . '/create', [$controller, 'createPost'])->add($auth);
    $app->get($path . '/{id}', [$controller, 'view'])->add($auth);
    $app->get($path . '/{id}/edit', [$controller, 'edit'])->add($auth);
    $app->post($path . '/{id}/edit', [$controller, 'editPost'])->add($auth);
    $app->get($path . '/{id}/delete', [$controller, 'delete'])->add($auth);
    $app->post($path . '/{id}/delete', [$controller, 'deletePost'])->add($auth);
}

$app->get('/tests/{testId}/results/create', [ResultController::class, 'create'])->add($auth);
$app->post('/tests/{testId}/results/create', [ResultController::class, 'createPost'])->add($auth);
$app->get('/tests/{testId}/results/{resultId}/edit', [ResultController::class, 'edit'])->add($auth);
$app->post('/tests/{testId}/results/{resultId}/edit', [ResultController::class, 'editPost'])->add($auth);
$app->get('/tests/{testId}/results/{resultId}/delete', [ResultController::class, 'delete'])->add($auth);
$app->post('/tests/{testId}/results/{resultId}/delete', [ResultController::class, 'deletePost'])->add($auth);

$app->get('/patients/{patientId}/certificates/create', [CertificateController::class, 'create'])->add($auth);
$app->post('/patients/{patientId}/certificates/create', [CertificateController::class, 'createPost'])->add($auth);
$app->get('/patients/{patientId}/certificates/{certificateId}/delete', [CertificateController::class, 'delete'])->add(
    $auth,
)->add($auth);
$app->post(
    '/patients/{patientId}/certificates/{certificateId}/delete',
    [CertificateController::class, 'deletePost'],
)->add($auth);

$app->get('/patients/{patientId}/reports/create', [ReportController::class, 'create'])->add($auth);
$app->post('/patients/{patientId}/reports/create', [ReportController::class, 'createPost'])->add($auth);
$app->get('/patients/{patientId}/reports/{reportId}/delete', [ReportController::class, 'delete'])->add($auth);
$app->post('/patients/{patientId}/reports/{reportId}/delete', [ReportController::class, 'deletePost'])->add($auth);

$app->run();
