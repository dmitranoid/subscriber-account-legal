<?php
declare(strict_types=1);


namespace App\Application\Service\Contract;


use App\Infrastructure\Persistence\Contract\SqlContractRepository;

final class ContractInfoService
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getContractInfo($contractNo)
    {
        $contractRepository = new SqlContractRepository($this->pdo);
        return $contractRepository->getById($contractNo);
    }
}