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
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('rp like \'119%\'')
            ->fetchAll();
    }

    public function getSumByContract($contractNom)
    {
        return $this->getAllSql()
            ->select('kp, sum(`sum`) paymentsum')
            ->where('kp', $contractNom)
            ->where('vids', ['V', 'W', 'R', 'Z'])
            ->where('rp like \'119%\'')
            ->group('kp')
            ->fetchAll();
    }

}