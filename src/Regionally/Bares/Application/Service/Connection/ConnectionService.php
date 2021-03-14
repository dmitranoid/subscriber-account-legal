<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Application\DTO\Connection\ReadingHistoryDto;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Regionally\Bares\Application\DtoAssembler\Connection\ConnectionInfo;
use App\Regionally\Bares\Application\DtoAssembler\Connection\ReadingHistory;
use App\Regionally\Bares\Infrastructure\Persistence\Connection\SqlConnectionArchRepository;
use App\Regionally\Bares\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use PDO;

class ConnectionService implements ConnectionServiceInterface
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $contractNom
     * @return ConnectionDto[]
     */
    public function getByContract($contractNom): ?array
    {
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        return $connectionsRepository->getByContract($contractNom);
    }

    public function getConnectionInfo(string $contractNom, string $abonentNom, string $tuName): ?ConnectionDto
    {
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        return ConnectionInfo::transform($connectionsRepository->getByNom($contractNom, $abonentNom, $tuName));
    }

    /**
     * @return ReadingHistoryDto[]
     */
    public function getReadingsHistory(string $contractNom, string $abonentNom, string $tuName): ?array
    {
        $connectionArchRepository = new SqlConnectionArchRepository($this->pdo);
//        $connectionArchRepository = new SqlConnectionArchRepository($this->histPdo);
        $readingsHistoryRaw = $connectionArchRepository->getByNom($contractNom, $abonentNom, $tuName);

        $readingsHistory = [];
        foreach ($readingsHistoryRaw as $item) {
            // FIXME узнать у Слюзюк, почкму пустые rppok
            if (empty($item['rppok'])) {
                continue;
            }
            array_unshift($readingsHistory, ReadingHistory::transform($item));
        }
        return $readingsHistory;
    }
}