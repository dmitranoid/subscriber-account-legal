<?php
declare(strict_types=1);


namespace App\Application\Service\Connection;


use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;

class ConnectionService
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getConnectionInfo($contractNom, $abonentNom, $tuName):?array
    {
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        return $connectionsRepository->getByNom($contractNom, $abonentNom, $tuName);
    }
}