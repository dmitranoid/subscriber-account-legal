<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\FoxproAbonentRepository;
use App\Infrastructure\Persistence\Connection\FoxproConnectionRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use XBase\Table;

class GetContractCounterReadingsAction extends ContractAction
{
    protected function action(): Response
    {
        $contractNom = $_SESSION['contract'] ?? null;

        $contractRepository = new FoxproContractRepository();
        $connectionsRepository = new FoxproConnectionRepository();
 
        $contract = $contractRepository->getByNomArr($contractNom);
        $connectionsList = $connectionsRepository->getByNom($contractNom, $counterNom);
 
        /* combine abonents and connections */

        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList){
            while ($abonent['nab'] == current($connectionsList)['nab']??'') {
               $abonent['connections'][] = array_shift($connectionsList);
            }
        });

        return $this->view->render($this->response, '/contract/connections.twig', ['contract'=>$contract, 'abonentsList'=>$abonentsList]);

    }

}