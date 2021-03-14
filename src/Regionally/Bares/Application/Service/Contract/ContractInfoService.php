<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Contract;


use App\Application\DTO\Contract\ContractDto;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Regionally\Bares\Application\DtoAssembler\Contract\ContractInfo as ContractInfoDtoAssembler;
use App\Regionally\Bares\Infrastructure\Persistence\Contract\SqlContractRepository;
use PDO;

final class ContractInfoService implements ContractInfoServiceInterface
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getContractInfo($contractNo): ContractDto
    {
        $contractRepository = new SqlContractRepository($this->pdo);
        return ContractInfoDtoAssembler::transform($contractRepository->getById($contractNo));
    }
}