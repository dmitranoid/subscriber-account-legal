<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Connection;


use App\Domain\Contract\Contract;
use XBase\Table;

class FoxproConnectionRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }

    public function getAll(): array
    {
        $ambtu = new Table(
            $this->dbPath . 'ambtu.dbf',
            null,
            'cp866'
        );
        $dataList = [];
        while (($record = $ambtu->nextRecord())) {

            $record = '';
        }
        return $dataList;
    }

    public function getByContract($contractNom)
    {
        $ambtu = new Table(
            $this->dbPath . 'ambtu.dbf',
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

    public function getByNom($contractNom, $counterNom)
    {
        $ambtu = new Table(
            $this->dbPath . 'ambtu.dbf',
            null,
            'cp866'
        );

        $data = [];
        while (($record = $ambtu->nextRecord())) {
            if ($record->kp == $contractNom && $record->nst == $counterNom) {
                $data = $record->getAllData();
                break;
            }
        }
        return $data;
    }
}
