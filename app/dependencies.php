<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $logger->pushProcessor(new UidProcessor());
            $logger->pushProcessor(new PsrLogMessageProcessor());

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        Twig::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $twigSettings = $settings['twig'];
            return Twig::create($twigSettings['path'], ['cache' => $twigSettings['cachePath']]);
        },
        PDO::class => function (ContainerInterface $c) {
            $dbSettings = $c->get('settings')['db'];
            $dbOperational = $dbSettings['operational'];
            $dbArchive = $dbSettings['archive'];
            $dbAdministration = $dbSettings['administration'];
            $dsn = $dbAdministration['driver'] . ":" . $dbAdministration['db'];
            try {
                $pdo = new PDO($dsn);
                $pdo->exec('ATTACH  \'' . $dbOperational['db'] . '\' as operational');
                $pdo->exec('ATTACH  \'' . $dbArchive['db'] .'\' as archive');
            } catch (Exception $e) {
                die('database open error');
            }
            // подключим архивы
            return $pdo;
        },
    ]);
};
