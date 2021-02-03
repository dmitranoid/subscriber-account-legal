<?php
declare(strict_types=1);


namespace App\Infrastructure\Service\Config;


use App\Application\Exceptions\AppException;

class ConfigLoader
{
    public static string $region;
    private static string $configRoot = '/app';

    public static function getConfigFor(string $configName)
    {
        $configFile = implode(DIRECTORY_SEPARATOR, [self::$configRoot, self::$region, $configName]) . '.php';
        if (!file_exists($configName)) {
            throw new AppException('config file ' . $configFile . ' not found');
        }
        include $configFile;
    }
}