<?php
declare(strict_types=1);


namespace App\Application\ServiceProviders;


use Psr\Container\ContainerInterface;

class PdoAdministrativeServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerInterface $c)
    {
        $dbSettings = $c->get('settings')['db'];
        $dbOperational = $dbSettings['operational'];
        $dbArchive = $dbSettings['archive'];
        $dbAdministration = $dbSettings['administration'];
        $dsn = $dbAdministration['driver'] . ":" . $dbAdministration['db'];
        try {
            $pdo = new \PDO($dsn);
            $pdo->exec('ATTACH  \'' . $dbOperational['db'] . '\' as operational');
            $pdo->exec('ATTACH  \'' . $dbArchive['db'] . '\' as archive');
        } catch (\Exception $e) {
            die('database open error');
        }
        // подключим архивы
        return $pdo;
    }

}