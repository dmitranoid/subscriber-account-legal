<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Payment;


use App\Infrastructure\Persistence\GenericSqlRepository;
use DateTime;

final class SqlPaymentRepository extends GenericSqlRepository
{
    protected $tableName = 'amposop';
    protected $identity = 'rp';

    public function getByContract($contractNom)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            //TODO переделать проверку на период rp like '119%' or rp like '120%'  год = 1900+rp
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('(rp like \'119%\' or rp like \'120%\')')
            ->fetchAll();
    }

    public function getSumByContract($contractNom, DateTime $periodStart, DateTime $periodEnd)
    {
        //TODO переделать проверку на период rp like '119%' or rp like '120%'  год = 1900+rp

        return $this->getAllSql()
            ->select('kp, sum(`sum`) sumTotal', true)
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('(rp like \'119%\' or rp like \'120%\')')
            ->group('kp')
            ->fetch();
    }

}