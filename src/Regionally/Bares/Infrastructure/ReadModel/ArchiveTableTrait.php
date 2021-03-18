<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel;


trait ArchiveTableTrait
{
    private string $archPeriod;
    private string $originalTableName;

    public function setPeriod(string $year, string $month)
    {
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