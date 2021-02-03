<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Regionally\Bares\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use PDO;

class ConnectionService
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $contractNom
     * @return ConnectionDto[] |null
     */
    public function getByContract($contractNom): ?array
    {
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        return $connectionsRepository->getByContract($contractNom);
    }

    public function getConnectionInfo($contractNom, $abonentNom, $tuName): ?array
    {
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        return $connectionsRepository->getByNom($contractNom, $abonentNom, $tuName);
    }


}