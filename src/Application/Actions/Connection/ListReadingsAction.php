<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\FoxproAbonentRepository;
use App\Infrastructure\Persistence\Connection\FoxproConnectionRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListReadingsAction extends ContractAction
{
    protected function action(): Response
    {
        $contractNom = $_SESSION['contract'] ?? null;

        $contractRepository = new FoxproContractRepository();
        $connectionsRepository = new FoxproConnectionRepository();
        $abonentsRepository = new FoxproAbonentRepository();

        $contract = $contractRepository->getByNomArr($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, function ($a, $b) { return $a['nab'] - $b['nab']; } );
        usort($connectionsList, function ($a, $b) { return $a['nab'] - $b['nab']; } );


        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList) {
            while ($abonent['nab'] == current($connectionsList)['nab'] ?? '') {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });
        // check file with sended readings
        $filename = sprintf("%s-%s.txt", $contractNom, date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        $readings = [];
        if (file_exists($dir . $filename)) {
            $readingsTxt = file_get_contents($dir . $filename);
            $readings = json_decode($readingsTxt, true);
        }

        return $this->view->render($this->response, '/contract/readings.twig', ['contract'=>$contract, 'abonentsList'=>$abonentsList, 'readings'=>$readings]);
    }

}