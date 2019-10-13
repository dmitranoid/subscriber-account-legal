<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Invoice;


use App\Domain\Contract\Contract;
use XBase\Table;

class FoxproInvoiceRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }

    public function getAll(): array
    {
        $dbfTable = new Table(
            $this->dbPath . 'amvyst.dbf',
            null,
            'cp866'
        );

        $dataList = [];
        while (($record = $dbfTable->nextRecord())) {
            $record = '';
        }

        return $dataList;
    }

    public function getByContract($contractNom)
    {
        $dbfTable = new Table(
            $this->dbPath . 'amvyst.dbf',
            null,
            'cp866'
        );


        $dataList = [];
        while (($record = $dbfTable->nextRecord())) {
            //фильтр:  акты, окончательный расчет & не отказана 
            if ($record->kp == $contractNom)
                if ((in_array($record->vpt, ['A', 'R']) && ($record->protkaz == 0 || trim($record->protkaz) == ''))) {
                $dataList[] = $record->getAllData();
            }
        }
        return $dataList;
    }
}
