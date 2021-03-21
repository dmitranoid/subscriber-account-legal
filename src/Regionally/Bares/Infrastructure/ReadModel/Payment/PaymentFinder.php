<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel\Payment;

use App\Regionally\Bares\Infrastructure\ReadModel\GenericSqlFinder;

final class PaymentFinder extends GenericSqlFinder
{
    protected string $tableName = 'amposop';
    protected $identity = 'rp';

    public function getByContract(string $contractNom)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            //TODO переделать проверку на период rp like '119%' or rp like '120%' на что-то работаюцее постоянно (год = 1900+rp)
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('substr(rp, 1, 2) = \'12\'')
            ->fetchAll();
    }

    public function getSumByContract($contractNom, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd)
    {
        //TODO переделать проверку на период rp like '119%' or rp like '120%'  год = 1900+rp

        return $this->getAllSql()
            ->select('kp, sum(`sum`) sumTotal', true)
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('substr(rp, 1, 2) = \'12\'')
            ->group('kp')
            ->fetch();
    }

}