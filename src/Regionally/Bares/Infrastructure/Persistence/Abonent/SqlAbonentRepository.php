<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\Persistence\Abonent;


use App\Infrastructure\Persistence\GenericSqlRepository;

final class SqlAbonentRepository extends GenericSqlRepository
{
    protected string $tableName = 'ambab';
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