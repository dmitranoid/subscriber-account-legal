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
            ->select('kp, rp, data, akt_nomer, sum(`sum`) sumTotal')
            ->where('kp', $contractNom)
//           ->where('vpt', ['A', 'R'])
            ->where('protkaz', '')
            ->where('rp like \'119%\'')
            ->group('kp, rp, data, akt_nomer')
            ->order('data, rp')
            ->fetchAll();
    }

    public function getSumByContract($contractNom, $periodStart = '20190101', $periodEnd = '20191231')
    {
        return $this->getAllSql()
            ->select('kp, sum(`sum`) invoicesum')
            ->where('kp', $contractNom)
            ->where('protkaz', '')
            ->where('rp like \'119%\'')
            ->group('kp')
            ->order('rp')
            ->fetchAll();
    }

}
