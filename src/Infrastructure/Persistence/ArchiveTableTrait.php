<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


trait ArchiveTableTrait
{
    private $archPeriod;

    public function setPeriod($year, $month){
        $this->archPeriod = $year . $month;
    }

    public function getTableName()
    {
        return $this->tableName . $this->archPeriod;
    }
}