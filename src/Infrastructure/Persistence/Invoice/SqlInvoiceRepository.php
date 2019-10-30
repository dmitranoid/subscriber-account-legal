<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Invoice;


use App\Infrastructure\Persistence\GenericSqlRepository;

final class SqlInvoiceRepository extends GenericSqlRepository
{

    protected $tableName = 'amvyst';
    protected $identity = 'kp';

    public function getByContract($contractNom)
    {
        return $this->getAllSql()
            ->where('kp', $contractNom)
            ->where('vpt', ['A', 'R'])
            ->where('protkaz', '')
            ->fetchAll();
    }

}
