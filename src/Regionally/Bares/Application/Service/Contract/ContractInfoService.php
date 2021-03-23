<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Contract;


use App\Application\DTO\Contract\ContractDto;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Regionally\Bares\Application\DtoTransformer\Contract\ContractInfo as ContractInfoDtoTransformer;
use App\Regionally\Bares\Infrastructure\ReadModel\Contract\ContractFinder;
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
        $contractFinder = new ContractFinder($this->pdo);
        return ContractInfoDtoTransformer::transform($contractFinder->getById($contractNo));
    }
}