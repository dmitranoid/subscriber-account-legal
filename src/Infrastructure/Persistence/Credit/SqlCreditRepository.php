<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Credit;


use App\Domain\Contract\Contract;
use App\Infrastructure\Persistence\GenericSqlRepository;
use XBase\Table;

final class SqlCreditRepository extends GenericSqlRepository
{
    public function getByContract($contractNom)
    {

    }

}