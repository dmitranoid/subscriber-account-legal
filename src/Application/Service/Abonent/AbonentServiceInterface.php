<?php


namespace App\Application\Service\Abonent;


use App\Application\DTO\Abonent\AbonentInfoDto;

interface AbonentServiceInterface
{
    /**
     * @param string $contractNom
     * @return AbonentInfoDto[]|null
     */
    public function getByContract(string $contractNom): ?array;

    public function getAbonentInfo(string $contractNom, string $abonentNom): ?AbonentInfoDto;
}