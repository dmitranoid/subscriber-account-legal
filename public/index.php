<?php
declare(strict_types=1);
ini_set('max_execution_time', '600');

ini_set('error_log',  __DIR__ . '/../storage/logs/php_errors.log');

ini_set("date.timezone","Europe/Minsk");

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\Helpers\AppHelper;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Infrastructure\Service\Session\Session;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/Application/Helpers/AppHelper.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (true) { // true in production
    $containerBuilder->enableCompilation(__DIR__ . '/../storage/cache/php-di/');
}

$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();


$region = AppHelper::detectRegion($container->get(Session::class));
// настройки для региона(филиала)
// ! применятся только если пользователь уже авторизирован
if (!empty($region) && file_exists(__DIR__ . '/../app/' . $region)) {
    $settings = require __DIR__ . '/../app/' . $region . '/settings.php';
    $settings($containerBuilder);
    $dependencies = require __DIR__ . '/../app/' . $region . '/dependencies.php';
    $dependencies($containerBuilder);
}

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

/** @var bool $displayErrorDetails */
$displayErrorDetails = $container->get('settings')['displayErrorDetails'] ?? true;

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Create Shutdown Handler
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

// Add Routing Middleware
$app->addRoutingMiddleware();
// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// init uriHelper
App\Application\Helpers\UriHelper::setRouteParser($app->getRouteCollector()->getRouteParser());

// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
