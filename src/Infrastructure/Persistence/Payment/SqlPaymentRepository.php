<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Payment;


use App\Infrastructure\Persistence\GenericSqlRepository;

final class SqlPaymentRepository extends GenericSqlRepository
{
    protected $tableName = 'amposop';
    protected $identity = 'rp';

    public function getByContract($contractNom)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('rp like \'119%\'')
            ->fetchAll();
    }

    public function getSumByContract($contractNom, \DateTime $periodStart, \DateTime $periodEnd)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            ->select('kp, sum(`sum`) sumTotal', true)
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('rp like \'119%\'')
            ->group('kp')
            ->fetch();
    }

}