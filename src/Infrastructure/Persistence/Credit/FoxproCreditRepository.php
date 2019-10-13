<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Credit;


use App\Domain\Contract\Contract;
use XBase\Table;

class FoxproCreditRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }

    function getAll(): array
    {
        $ambtu = new Table(
            $this->dbPath . 'amdolg.dbf',
            null,
            'cp866');

        $dataList = [];
        while (($record = $ambtu->nextRecord())) {

            $record = '';
        }

        return $dataList;
    }

    public function getByContract($contractNom)
    {
        $ambtu = new Table(
            $this->dbPath . 'amdolg.dbf',
            null,
            'cp866');

        $dataList = [];
        while (($record = $ambtu->nextRecord())) {
            if ($record->kp != $contractNom) {
                continue;
            }
            $dataList[] = $record->getAllData();
        }
        return $dataList;
    }

}