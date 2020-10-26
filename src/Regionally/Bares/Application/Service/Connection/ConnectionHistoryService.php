<?php
declare(strict_types=1);


namespace App\Application\Service\Connection;


use App\Infrastructure\Persistence\Connection\SqlConnectionArchRepository;
use PDO;

final class ConnectionHistoryService
{
    /** @var PDO */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getReadingsHistory($contractNom, $abonentNom, $tuName):?array
    {
        $connectionArchRepository = new SqlConnectionArchRepository($this->pdo);
        $readingsHistory = $connectionArchRepository->getByNom($contractNom, $abonentNom, $tuName);
        array_walk(
            $readingsHistory,
            function(&$item) {
                $item['consumption'] = floatval($item['tpok']) - floatval($item['ppok']);
                $item['rp_part_1'] = substr($item['rppok'], 0,1);
                $item['rp_part_m'] = substr($item['rppok'], 3,2);
                $item['rp_part_y'] = substr($item['rppok'], 1,2);
            }
        );
        return array_reverse($readingsHistory);
    }

}