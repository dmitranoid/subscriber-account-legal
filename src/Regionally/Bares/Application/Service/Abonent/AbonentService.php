<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Abonent;


use App\Application\DTO\Abonent\AbonentInfoDto;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Regionally\Bares\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use PDO;

class AbonentService implements AbonentServiceInterface
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByContract(string $contractNom): ?array
    {
        return (new SqlAbonentRepository($this->pdo))->getByContract($contractNom);
    }

    public function getAbonentInfo(string $contractNom, string $abonentNom): ?AbonentInfoDto
    {
        return (new SqlAbonentRepository($this->pdo))->getByNom($contractNom, $abonentNom);
    }
}