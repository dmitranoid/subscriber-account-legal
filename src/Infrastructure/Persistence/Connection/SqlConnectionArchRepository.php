<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Connection;


use App\Infrastructure\Persistence\ArchiveTableTrait;
use App\Infrastructure\Persistence\GenericSqlRepository;

final class SqlConnectionArchRepository extends GenericSqlRepository
{
    use ArchiveTableTrait;

    protected $tableName = 'ambtu';

    /**
     * получить список периодов
     * костыль !!!!
     *
     */
    private function getPeriods()
    {
        return [
            ['2019','01'],
            ['2019','02'],
            ['2019','03'],
            ['2019','04'],
            ['2019','05'],
            ['2019','06'],
            ['2019','07'],
            ['2019','08'],
            ['2019','09'],
            ['2019','10'],
/*
            '201901',
            '201902',
            '201903',
            '201904',
            '201905',
            '201906',
            '201907',
            '201908',
            '201909',
*/
        ];
    }

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
}
