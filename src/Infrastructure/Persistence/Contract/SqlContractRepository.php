<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Contract;


use App\Domain\Contract\ContractRepository;
use App\Infrastructure\Persistence\GenericSqlRepository;

class SqlContractRepository extends GenericSqlRepository implements ContractRepository
{
    protected $tableName = 'splat';
    protected $identity = 'kp';

}