<?php
declare(strict_types=1);

namespace App\Application\Helpers;


use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteParser;

class UriHelper
{
    /** @var RouteParserInterface */
    private static $routeParser;
    static function setRouteParser(RouteParserInterface $slimRouteParser):void
    {
        self::$routeParser = $slimRouteParser;
    }

    public static function urlFor(string $routeName):string
    {
        return self::$routeParser->urlFor($routeName);
    }
}