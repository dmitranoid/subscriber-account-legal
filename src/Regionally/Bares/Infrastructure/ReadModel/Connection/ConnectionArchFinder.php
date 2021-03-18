<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel\Connection;


use App\Regionally\Bares\Infrastructure\ReadModel\ArchiveTableTrait;
use App\Regionally\Bares\Infrastructure\ReadModel\GenericSqlFinder;
use DateTime;

final class ConnectionArchFinder extends GenericSqlFinder
{
    use ArchiveTableTrait;

    protected string $tableName = 'ambtu';

    public function getByContract($contractNom)
    {
        $archData = [];
        foreach ($this->getPeriods() as $archPeriod) {
            $this->setPeriod($archPeriod[0], $archPeriod[1]);
            array_push(
                $archData,
                $this->getAllSql()
                    ->where('kp', $contractNom)
                    ->fetchAll()
            );
        }
        return $archData;
    }

    public function getByNom($contractNom, $abonentNom, $counterNom)
    {
        $archData = [];
        foreach ($this->getPeriods() as $archPeriod) {
            $this->setPeriod($archPeriod[0], $archPeriod[1]);
            $sql = $this->getAllSql()
                ->where('kp', $contractNom)
                ->where('nab', $abonentNom)
                ->where('naim', $counterNom);

            $currData = $sql->fetchAll();
            if ($currData) {
                $archData = array_merge(
                    $archData,
                    $currData
                );
            }
        }
        return $archData;
    }

    private function getPeriods()
    {
        $startYear = 2019;
        $periods = [];
        for ($year = $startYear; $year <= (int)(new DateTime())->format('Y'); $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $periods[] = [(string)$year, sprintf('%02d', $month)];
            }
        }
        return $periods;
    }
}
