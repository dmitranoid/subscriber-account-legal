<?php
declare(strict_types=1);


namespace App\Application\Service\Connection;


use App\Application\DTO\Connection\ConnectionDto;

interface ConnectionServiceInterface
{
    /**
     * @param $contractNom
     * @return ConnectionDto[] |null
     */
    public function getByContract($contractNom): ?array;

    public function getConnectionInfo($contractNom, $abonentNom, $tuName): ?ConnectionDto;
}