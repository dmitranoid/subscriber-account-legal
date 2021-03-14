<?php
declare(strict_types=1);


use DI\ContainerBuilder;


return static function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => DI\add([
            'db' => [
                'operational' => [
                    'driver' => 'sqlite',
                    'db' => realpath(__DIR__ . '/../../data/sqlite/legal-operational.sqlite3'),
                    'username' => '',
                    'password' => '',
                ],
                'archive' => [
                    'driver' => 'sqlite',
                    'db' => realpath(__DIR__ . '/../../data/sqlite/legal-arch.sqlite3'),
                    'username' => '',
                    'password' => '',
                ],
                'administration' => [
                    'driver' => 'sqlite',
                    'db' => realpath(__DIR__ . '/../../data/sqlite/legal-administration.sqlite3'),
                    'username' => '',
                    'password' => '',
                ],
            ],
        ]),
    ]);
};