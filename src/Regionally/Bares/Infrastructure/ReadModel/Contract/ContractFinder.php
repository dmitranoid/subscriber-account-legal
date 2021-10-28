<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Infrastructure\ReadModel\Contract;


use App\Domain\Contract\ContractRepository;
use App\Infrastructure\Persistence\GenericSqlRepository;

class ContractFinder extends GenericSqlRepository
{
    protected string $tableName = 'splat';
    protected $identity = 'kp';


    public function getIndexList()
    {
        return $this->getAllSql()
            ->select('splat.kp, np1, npp1, npp2, tip_rg, adres1, tip_np, adres2, count(ambtu.naim) tucnt', true)
            ->leftJoin('ambtu on splat.kp = ambtu.kp')
            ->group('splat.kp, np1, npp1, npp2, tip_rg, adres1, tip_np, adres2')
            ->order('splat.kp')
            ->fetchAll();
    }

    public function getByCode(string $code): ?array
    {
        return $this->getById($code);
    }
}