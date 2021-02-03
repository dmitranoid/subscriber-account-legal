<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\Persistence\Connection;


use App\Regionally\Bares\Infrastructure\Persistence\ArchiveTableTrait;
use App\Regionally\Bares\Infrastructure\Persistence\GenericSqlRepository;

final class SqlConnectionArchRepository extends GenericSqlRepository
{
    use ArchiveTableTrait;

    protected $tableName = 'ambtu';

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

    /**
     * получить список периодов
     * костыль !!!!
     *
     */
    private function getPeriods()
    {
        return [
            ['2019', '01'],
            ['2019', '02'],
            ['2019', '03'],
            ['2019', '04'],
            ['2019', '05'],
            ['2019', '06'],
            ['2019', '07'],
            ['2019', '08'],
            ['2019', '09'],
            ['2019', '10'],
            ['2019', '11'],
            ['2019', '12'],
            ['2020', '01'],
            ['2020', '02'],
            ['2020', '03'],
            ['2020', '04'],
            ['2020', '05'],
            ['2020', '06'],
            ['2020', '07'],
        ];
    }
}
