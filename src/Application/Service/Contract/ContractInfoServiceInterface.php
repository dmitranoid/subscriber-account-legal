<?php


namespace App\Application\Service\Contract;


use App\Application\DTO\Contract\ContractInfoDto;

interface ContractInfoServiceInterface
{
    public function getContractInfo($contractNo): ContractInfoDto;
}