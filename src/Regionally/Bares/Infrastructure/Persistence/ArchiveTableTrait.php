<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence;


trait ArchiveTableTrait
{
    private $archPeriod;
    private $originalTableName;

    public function setPeriod($year, $month){
        $this->archPeriod = $year . $month;
        if (empty($this->originalTableName)) {
            $this->originalTableName = $this->tableName;
        }
        $this->tableName = $this->originalTableName . '_' . $this->archPeriod;
    }
/*
    protected function getTableName():string
    {
        return $this->tableName . '_' . $this->archPeriod;
    }
*/
}