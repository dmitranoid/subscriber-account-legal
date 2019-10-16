<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\FoxproAbonentRepository;
use App\Infrastructure\Persistence\Connection\FoxproConnectionRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewContractConnectionAction extends ContractAction
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

        list($abonentNo, $tuName)= $this->extractAbTu($this->request->getAttribute('abtu', ''));

        $contractRepository = new FoxproContractRepository();
        $connectionsRepository = new FoxproConnectionRepository();
        $abonentsRepository = new FoxproAbonentRepository();

        $contract = $contractRepository->getByNomArr($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, function($a, $b){return $a['nab'] - $b['nab'];});
        usort($connectionsList, function($a, $b){return $a['nab'] - $b['nab'];});


        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList){
            while ($abonent['nab'] == current($connectionsList)['nab']??'') {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });

        return $this->view->render($this->response, '/contract/connections.twig', ['contract'=>$contract, 'abonentsList'=>$abonentsList]);

    }

}