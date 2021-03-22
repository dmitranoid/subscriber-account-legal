<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Application\DTO\Connection\ReadingHistoryDto;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Regionally\Bares\Application\DtoAssembler\Connection\ConnectionInfo;
use App\Regionally\Bares\Application\DtoAssembler\Connection\ReadingHistory;
use App\Regionally\Bares\Infrastructure\ReadModel\Connection\ConnectionArchFinder;
use App\Regionally\Bares\Infrastructure\ReadModel\Connection\ConnectionFinder;
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
        $connectionFinder = new ConnectionFinder($this->pdo);
        $connections = [];
        foreach ($connectionFinder->getByContract($contractNom) ?? [] as $item) {
            $connections[] = ConnectionInfo::transform($item);
        }
        return $connections;
    }

    public function getConnectionInfo(string $contractNom, string $abonentNom, string $tuName): ?ConnectionDto
    {
        $connectionFinder = new ConnectionFinder($this->pdo);
        return ConnectionInfo::transform($connectionFinder->getByNom($contractNom, $abonentNom, $tuName));
    }

    /**
     * @return ReadingHistoryDto[]
     */
    public function getReadingsHistory(string $contractNom, string $abonentNom, string $tuName): ?array
    {
        $connectionArchFinder = new ConnectionArchFinder($this->pdo);
//        $connectionArchFinder = new SqlConnectionArchRepository($this->histPdo);
        $readingsHistoryRaw = $connectionArchFinder->getByNom($contractNom, $abonentNom, $tuName);

        $readingsHistory = [];
        foreach ($readingsHistoryRaw as $item) {
            // rppok до 03-2021 пустой если не платили в этом месяце, после 03-2021 хранит последний месяц оплаты
            if (empty($item['rppok'])) {
                continue;
            }
            array_unshift($readingsHistory, ReadingHistory::transform($item));
        }
        return $readingsHistory;
    }
}