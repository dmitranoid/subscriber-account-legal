<?php
declare(strict_types=1);


use DI\ContainerBuilder;


return static function (ContainerBuilder $containerBuilder) {
    // Local Settings
    $storageRoot = __DIR__ . '/../../storage/';
    $containerBuilder->addDefinitions([
        'localSettings' => DI\add([
            'dirs' => [
//            'documentsStorageRoot' => 'F:/lkprom-docs/', // под windows буква диска с большой буквы !!!
                'documentsStorageRoot' => 'D:/work/legal-docs/', // под windows буква диска с большой буквы !!!
                'readingsStorageRoot' => $storageRoot . 'bar/readings/',
                'readingsCsvRoot' => $storageRoot . 'bar/readings-csv/',
                'readingsTempDir' => $storageRoot . 'bar/readings-tmp/',
                'docsListCache' => $storageRoot . 'cache/doclist',
            ],
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
            ],
        ]),
    ]);
};