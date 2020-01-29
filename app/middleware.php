<?php
declare(strict_types=1);

use App\Application\Middleware\TwigFlashMessageMiddleware;
use Slim\App;
use App\Application\Middleware\SessionMiddleware;

return function (App $app) {
    $app->add(TwigFlashMessageMiddleware::class);
    $app->add(SessionMiddleware::class);
};
