<?php
declare(strict_types=1);


namespace App\Domain\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;

class ContractNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'Contract not found';
}