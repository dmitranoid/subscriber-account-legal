<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Service\Connection\ConnectionHistoryService;
use App\Application\Service\Contract\ContractInfoService;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewConnectionAction extends Action
{
    private function extractAbTu($str):array
    {
        $delimPos = mb_strpos($str, '-');
        return [
            mb_substr($str, 0 ,$delimPos),
            mb_substr($str, $delimPos+1, mb_strlen($str)-$delimPos)
        ];
    }

    protected function action(): Response
    {
        $contractNom = $_SESSION['contract'] ?? null;
        // историю показаний можно собрать только из архивной базы
        $archPdo = new \PDO('sqlite:'. realpath($_SERVER['DOCUMENT_ROOT'] . '\..\data\sqlite\legal-arch.sqlite3'));

        list($abonentNom, $tuName)= $this->extractAbTu($this->request->getAttribute('abtu', ''));

        $connectionHistory = new ConnectionHistoryService($archPdo);

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);


        $contract = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        $abonent = $abonentsRepository->getByNom($contractNom, $abonentNom);
        $connection = $connectionsRepository->getByNom($contractNom, $abonentNom, $tuName);

        $readingsHistory = $connectionHistory->getReadingsHistory($contractNom, $abonentNom, $tuName);

            return $this->view->render(
            $this->response,
            'connection/connection.twig',
            [
                'contract' => $contract,
                'abonent' => $abonent,
                'connection' => $connection,
                'readingsHistory' => $readingsHistory,
            ]
        );

    }

}