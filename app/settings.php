<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
//            'urlPrefix' =>route 'lkprom/',
            'displayErrorDetails' => true,
            'logger' => [
                'name' => 'lkprom',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'twig' => [
                'path' => __DIR__ . '/../templates/',
                'cachePath' => __DIR__ . '/../storage/cache/twig/',
            ],
        ],
    ]);
};
