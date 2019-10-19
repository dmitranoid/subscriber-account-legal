<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Abonent;


use XBase\Table;

class FoxproAbonentRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }


    public function getAll(): array
    {
        $ambtu = new Table(
            $this->dbPath . 'ambab.dbf',
            null,
            'cp866'
        );

        $dataList = [];
        while (($record = $ambtu->nextRecord())) {

            $record = '';
        }

        return $dataList;
    }

    public function getById($contractNom):array
    {
        $ambtu = new Table(
            $this->dbPath . 'ambab.dbf',
            null,
            'cp866'
        );

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