<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'twig' => [
                'path' => __DIR__ . '/../templates/',
                'cachePath' => false,
            ],
            'dbf-data' => [
            ],
            'db' => [
                'dbf' => [
                    'dir' =>  realpath(__DIR__ . '/../data/dbf'),
                ],
                'operational' => [
                    'driver' => 'sqlite',
                    'db'=> __DIR__ . '/../data/sqlite/legal-operational.sqlite3',
                    'username' => '',
                    'password' => '',
                ],
                'archive' => [
                    'driver' => 'sqlite',
                    'db' => __DIR__ . '/../data/sqlite/legal-arch.sqlite3',
                    'username' => '',
                    'password' => '',
                ]
            ],
            'invoiceDir' => '\\vms8\prom_arch\AMSRV\schet\\',
        ],
    ]);
};
