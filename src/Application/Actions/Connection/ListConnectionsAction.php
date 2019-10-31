<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Service\Contract\ContractInfoService;
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

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        array_walk($connectionsList, function (&$item) {
            $tuUrl = $item['naim'];
            $tuUrl = str_replace('.', 'dot', $tuUrl);
            $tuUrl = str_replace('/', 'slashr', $tuUrl);
            $tuUrl = str_replace('\\', 'slashl', $tuUrl);
            $item['naim_url'] = $tuUrl;
        });

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