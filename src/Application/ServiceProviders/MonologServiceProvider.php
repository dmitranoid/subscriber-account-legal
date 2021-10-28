<?php
declare(strict_types=1);


namespace App\Application\ServiceProviders;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;

class MonologServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerInterface $c)
    {
        $settings = $c->get('settings');

        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);

        $logger->pushProcessor(new UidProcessor());
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
    }

}