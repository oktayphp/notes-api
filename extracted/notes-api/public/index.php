<?php
declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;
use App\Dependencies;
use App\Controller\NotesController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Nyholm\Psr7\Factory\Psr17Factory;

require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv->load();
}

$container = new Container();
AppFactory::setContainer($container);
AppFactory::setPsr17Factory(new Psr17Factory());

$settings = (require __DIR__ . '/../src/Dependencies.php')($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// error handling could be added here (custom error middleware)

$app->get('/notes', [NotesController::class, 'list']);
$app->get('/notes/{id:[0-9]+}', [NotesController::class, 'get']);

$app->group('', function (RouteCollectorProxy $group){
    $group->post('/notes', [NotesController::class, 'create']);
    $group->put('/notes/{id:[0-9]+}', [NotesController::class, 'update']);
    $group->delete('/notes/{id:[0-9]+}', [NotesController::class, 'delete']);
})->add(new AuthMiddleware($_ENV['SECRET_KEY']));

$app->post('/auth/login', [\App\Controller\AuthController::class, 'login']);

$app->run();