<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\Persistence\Invoice;


use App\Regionally\Bares\Infrastructure\Persistence\GenericSqlRepository;
use DateTime;

final class SqlInvoiceRepository extends GenericSqlRepository
{

    protected $tableName = 'amvyst';
    protected $identity = 'kp';

    public function getByContract($contractNom)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            ->select('kp, rp, data, akt_nomer, sum(`sum`) sumTotal')
            ->where('kp', $contractNom)
//           ->where('vpt', ['A', 'R'])     // виды платежных требований
            ->where('protkaz', '')          // если не пустое, то отказная платежка
            ->where('(rp like \'119%\' or rp like \'120%\' or rp like \'121%\' )')
            ->group('kp, rp, data, akt_nomer')
            ->order('data, rp')
            ->fetchAll();
    }

    public function getSumByContract($contractNom, DateTime $periodStart, DateTime $periodEnd)
    {
        //TODO переделать проверку на период
        return $this->getAllSql()
            ->select('kp, sum(`sum`) sumTotal', true)
            ->where('kp', $contractNom)
            ->where('protkaz', '')
            ->where('(rp like \'119%\' or rp like \'120%\ or rp like \'121%\')')
            ->group('kp')
            ->order('rp')
            ->fetch();
    }

}
