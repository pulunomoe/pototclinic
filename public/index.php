<?php

session_start();

use App\Controller\DoctorController;
use App\Controller\LoginController;
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

$app->get('/doctors', [DoctorController::class, 'index']);
$app->get('/doctors/create', [DoctorController::class, 'create']);
$app->post('/doctors/create', [DoctorController::class, 'createPost']);
$app->get('/doctors/{id}', [DoctorController::class, 'view']);
$app->get('/doctors/{id}/edit', [DoctorController::class, 'edit']);
$app->post('/doctors/{id}/edit', [DoctorController::class, 'editPost']);
$app->get('/doctors/{id}/delete', [DoctorController::class, 'delete']);
$app->post('/doctors/{id}/delete', [DoctorController::class, 'deletePost']);

$app->run();
