<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Abonent;


use App\Infrastructure\Persistence\GenericSqlRepository;
use Envms\FluentPDO\Query;

final class SqlAbonentRepository extends GenericSqlRepository
{
    protected $tableName='splat';
    protected $identity = ['kp', 'nab'];

}