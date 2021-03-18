<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Abonent;


use App\Application\DTO\Abonent\AbonentInfoDto;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Regionally\Bares\Application\DtoAssembler\Abonent\AbonentInfo;
use App\Regionally\Bares\Infrastructure\ReadModel\Abonent\AbonentFinder;
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
        $abonents = [];
        foreach ((new AbonentFinder($this->pdo))->getByContract($contractNom) ?? [] as $item) {
            $abonents[] = AbonentInfo::transform($item);
        }
        return $abonents;
    }

    public function getAbonentInfo(string $contractNom, string $abonentNom): ?AbonentInfoDto
    {
        $data = (new AbonentFinder($this->pdo))->getByNom($contractNom, $abonentNom);
        return AbonentInfo::transform($data);
    }
}