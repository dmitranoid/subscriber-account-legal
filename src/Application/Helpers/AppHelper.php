<?php
declare(strict_types=1);


namespace App\Application\Helpers;


use App\Infrastructure\Service\Session\Session;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class AppHelper
{
    public static function container(): ContainerInterface
    {
        return self::app()->getContainer();
    }

    public static function app(): App
    {
        return AppFactory::create();
    }

    public static function detectRegion(Session $session): ?string
    {
        return $session->get('region');
    }
}
