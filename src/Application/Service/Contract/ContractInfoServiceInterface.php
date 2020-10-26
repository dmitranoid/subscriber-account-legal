<?php


namespace App\Application\Service\Contract;


use App\Application\DTO\Contract\ContractDto;

interface ContractInfoServiceInterface
{
    public function getContractInfo($contractNo): ContractDto;
}