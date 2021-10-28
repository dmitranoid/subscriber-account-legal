<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel\Abonent;


use App\Regionally\Bares\Infrastructure\ReadModel\GenericSqlFinder;

final class AbonentFinder extends GenericSqlFinder
{
    protected string $tableName = 'ambab';
    protected $identity = ['kp', 'nab'];


    public function getByContract(string $contractNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->fetchAll();
    }

    public function getByNom(string $contractNom, string $abonentNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('nab', $abonentNom)
            ->fetch();
    }
}