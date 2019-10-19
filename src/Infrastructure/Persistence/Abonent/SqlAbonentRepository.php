<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Abonent;


use App\Infrastructure\Persistence\GenericSqlRepository;
use Envms\FluentPDO\Query;

final class SqlAbonentRepository extends GenericSqlRepository
{
    protected $tableName='ambab';
    protected $identity = ['kp', 'nab'];


    public function getByContract($contractNo)
    {
        return $this->getAllSql()
            ->where('kp', $contractNo)
            ->fetchAll();
    }
}