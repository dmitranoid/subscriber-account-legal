<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListConnectionsAction extends Action
{
    protected function action(): Response
    {
        $contractNom = $_SESSION['contract'] ?? null;

        $contractRepository = new SqlContractRepository($this->pdo);
        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = $contractRepository->getById($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, function($a, $b){return $a['nab'] - $b['nab'];});
        usort($connectionsList, function($a, $b){
            return $a['nab'] - $b['nab'];
        });

        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList){
            while ($abonent['nab'] == current($connectionsList)['nab']??'') {
               $abonent['connections'][] = array_shift($connectionsList);
            }
        });

        return $this->view->render($this->response, 'connection/connections.twig', ['contract'=>$contract, 'abonentsList'=>$abonentsList]);

    }

}