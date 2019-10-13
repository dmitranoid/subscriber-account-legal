<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Payment;


use App\Domain\Contract\Contract;
use XBase\Table;

class FoxproPaymentRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }

    public function getAll(): array
    {
        $ambtu = new Table(
            $this->dbPath . 'amposop.dbf',
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
            $this->dbPath . 'amposop.dbf',
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