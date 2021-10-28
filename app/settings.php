<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder) {
    // Global Settings
    $containerBuilder->addDefinitions([
        'dirs' => [
            'storageRoot' => __DIR__ . '../storage/',
        ],
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
            'db' => [
                'administration' => [
                    'driver' => 'sqlite',
                    'db' => realpath(__DIR__ . '/../../data/sqlite/legal-administration.sqlite3'),
                    'username' => '',
                    'password' => '',
                ],
            ],
        ],
    ]);
};
