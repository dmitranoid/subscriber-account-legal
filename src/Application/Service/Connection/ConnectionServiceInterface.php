<?php
declare(strict_types=1);


namespace App\Application\Service\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Application\DTO\Connection\ReadingHistoryDto;

interface ConnectionServiceInterface
{
    /**
     * @return ConnectionDto[]|null
     */
    public function getByContract(string $contractNom): ?array;

    public function getConnectionInfo(string $contractNom, string $abonentNom, string $tuName): ?ConnectionDto;

    /**
     * @return ReadingHistoryDto[]|null
     */
    public function getReadingsHistory(string $contractNom, string $abonentNom, string $tuName): ?array;
}