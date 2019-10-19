<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Contract;


use App\Domain\Contract\Contract;
use App\Domain\Contract\ContractNotFoundException;
use App\Domain\Contract\ContractRepository;
use XBase\Table;

class FoxproContractRepository
{
    private $dbPath;

    public function __construct($dbPath = '../data/dbf/')
    {
        $this->dbPath = $dbPath;
    }

    function getAll(): array
    {
        $table = new Table(
            $this->dbPath . 'splat.dbf',
            null,
            'cp866');
        $contractInfo = [];
        $founded = false;
        while (($record = $table->nextRecord())) {
            $contract = new Contract(
                $record->kp,
                $record->dop,
                $record->getDate('dd'),
                $record->npp1 . $record->npp2,
                $record->fio,
                $record->nt
            );
            $contractList = $contract;
        }

        return $contractList;
    }


    function getByNom($contractNo):Contract
    {
        $table = new Table(
            $this->dbPath . 'splat.dbf',
            null,
            'cp866');
        $founded = false;
        while (($record = $table->nextRecord()) && !$founded) {
            if ($record->kp != $contractNo) {
                continue;
            }
            $founded = true;
            //$contract = new Contract($record->getAllData());
        }
        if (!$founded) {
            throw new ContractNotFoundException();
        }
        return $contract;
    }

    function getAllArr(): array
    {
        $table = new Table(
            $this->dbPath . 'splat.dbf',
            null,
            'cp866');
        $contractInfo = [];
        $founded = false;
        while (($record = $table->nextRecord())) {
            $contractList[] = $record->getAllData();
        }

        return $contractList;
    }

    function getByNomArr($contractNo):array
    {
        $table = new Table(
            $this->dbPath . 'splat.dbf',
            null,
            'cp866');
        $founded = false;
        while (($record = $table->nextRecord()) && !$founded) {
            if ($record->kp != $contractNo) {
                continue;
            }
            $founded = true;
            $contract = $record->getAllData();
        }
        if (!$founded) {
            throw new ContractNotFoundException();
        }
        return $contract;
    }
}