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
            ->fetchAll();
    }

}