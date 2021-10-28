<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\ServiceProviders;


use App\Application\ServiceProviders\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class PdoPromProvider implements ServiceProviderInterface
{
    public static function register(ContainerInterface $c)
    {
        $dbSettings = $c->get('settings')['db'];
        $dbOperational = $dbSettings['operational'];
        $dbArchive = $dbSettings['archive'];
        $dsn = $dbOperational['driver'] . ":" . $dbOperational['db'];
        try {
            $pdo = new \PDO($dsn);
//            $pdo->exec('ATTACH  \'' . $dbOperational['db'] . '\' as operational');
            $pdo->exec('ATTACH  \'' . $dbArchive['db'] . '\' as archive');
        } catch (\Exception $e) {
            die('barprom database open error');
        }
        return $pdo;
    }

}