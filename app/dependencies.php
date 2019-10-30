<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Application\View\ViewInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        Twig::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $twigSettings = $settings['twig'];
            return new Twig($twigSettings['path'], ['cache' => $twigSettings['cachePath']]);
        },
        PDO::class => function (ContainerInterface $c) {
            $dbSettings = $c->get('settings')['db']['operational'];
            $dsn = $dbSettings['driver'] . ":" . $dbSettings['db'];
            try {
                $pdo = new PDO($dsn);
            } catch (Exception $e) {
                die('database open error');
            }
            return $pdo;
        },
    ]);
};
