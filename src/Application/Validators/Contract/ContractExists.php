<?php
declare(strict_types=1);


namespace App\Application\Validators\Contract;


use App\Application\Validators\ValidatorInterface;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;

class ContractExists implements ValidatorInterface
{
    /**
     * @var SqlContractRepository
     */
    private $contractRepository;

    private $contractCode;

    public function __construct(\PDO $pdo)
    {
        $this->contractRepository = new SqlContractRepository($pdo);
    }

    public function setData($contractCode): void
    {
        $this->contractCode = $contractCode;
    }

    public function validate(): bool
    {
        $contract = $this->contractRepository->getByCode($this->contractCode);
        return !(false === $contract || empty($contract));
    }

}