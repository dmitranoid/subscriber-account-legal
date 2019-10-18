<?php
declare(strict_types=1);


namespace App\Domain\Contract;


interface ContractRepository
{
    /**
     * @return Contract[]
     */
    function getAll():array;

    /**
     * @param $contractNo
     * @return Contract
     * @throws ContractNotFoundException
     */
    function getById($contractNo):array ;
}