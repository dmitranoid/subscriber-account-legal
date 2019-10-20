<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Connection;


use App\Infrastructure\Persistence\GenericSqlRepository;

final class SqlConnectionRepository extends GenericSqlRepository
{

    protected $tableName = 'ambtu';


    public function getByContract($contractNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->fetchAll();
    }

    public function getByNom($contractNom, $abonentNom, $counterNom)
    {
        $data = $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('nab', $abonentNom)
            ->where('naim', $counterNom)
            ->fetch();
        return $data;
    }
}
