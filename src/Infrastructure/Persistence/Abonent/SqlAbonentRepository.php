<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Abonent;


use App\Infrastructure\Persistence\GenericSqlRepository;
use Envms\FluentPDO\Query;

final class SqlAbonentRepository extends GenericSqlRepository
{
    protected $tableName='ambab';
    protected $identity = ['kp', 'nab'];


    public function getByContract($contractNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->fetchAll();
    }

    public function getByNom($contractNom, $abonentNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('nab', $abonentNom)
            ->fetch();

    }
}