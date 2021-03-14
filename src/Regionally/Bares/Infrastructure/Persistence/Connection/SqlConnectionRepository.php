<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\Persistence\Connection;


use App\Regionally\Bares\Infrastructure\Persistence\GenericSqlRepository;

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
            ->select('ambtu.*, amtsch.*, amstar.naim tarnaim')
            ->leftJoin('amtsch on amtsch.ksch = ambtu.ksch')
            ->leftJoin('amstar on amstar.nt = ambtu.nt1')
            ->where('ambtu.kp', $contractNom)
            ->where('ambtu.nab', $abonentNom)
            ->where('ambtu.naim', $counterNom)
            ->fetch();
        return $data;
    }
}
