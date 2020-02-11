<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewListReadingsAction extends Action
{
    protected function action(): Response
    {
        $session = $this->request->getAttribute('session', []);
        $auth = new UserAuthService($this->logger, $this->pdo, $session);
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, function ($a, $b) { return $a['nab'] - $b['nab']; } );
        usort($connectionsList, function ($a, $b) { return $a['nab'] - $b['nab']; } );


        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList) {
            while ($abonent['nab'] == (current($connectionsList)['nab'] ?? '')) {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });
        // check file with sended readings
        $filename = sprintf("%s-%s.json", $contractNom, date('Ym'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        $readings = [];
        if (file_exists($dir . $filename)) {
            $readingsTxt = file_get_contents($dir . $filename);
            $readings = json_decode($readingsTxt, true);
        }

        return $this->view->render($this->response, 'front/connection/readings.twig', ['contract'=>$contract, 'abonentsList'=>$abonentsList, 'readings'=>$readings]);
    }

}