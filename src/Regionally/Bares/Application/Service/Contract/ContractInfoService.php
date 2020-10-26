<?php
declare(strict_types=1);


namespace App\Application\Service\Contract;


use App\Application\DTO\Contract\ContractInfoDto;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use App\Regionally\Bares\Application\DtoAssembler\Contract\ContractInfo as ContractInfoDtoAssembler;
use PDO;

final class ContractInfoService implements ContractInfoServiceInterface
{
    /** @var PDO */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getContractInfo($contractNo): ContractInfoDto
    {
        $contractRepository = new SqlContractRepository($this->pdo);
        return ContractInfoDtoAssembler::transform($contractRepository->getById($contractNo));
    }
}