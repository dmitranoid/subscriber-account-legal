<?php
declare(strict_types=1);


namespace App\Application\ServiceProviders;


use Psr\Container\ContainerInterface;

class PdoAdministrativeServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerInterface $c)
    {
        $dbSettings = $c->get('settings')['db'];
        $dbAdministration = $dbSettings['administration'];
        $dsn = $dbAdministration['driver'] . ":" . $dbAdministration['db'];
        try {
            $pdo = new \PDO($dsn);
        } catch (\Exception $e) {
            die('administrative database open error');
        }
        return $pdo;
    }

}