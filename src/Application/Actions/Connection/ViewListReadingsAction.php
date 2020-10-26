<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Readings\ReadingsService;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use App\Infrastructure\Service\Session\Session;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;

class ViewListReadingsAction extends Action
{
    protected function action(): Response
    {
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error(
                'не найден договор {contractNom}, пользователь {login}',
                [
                    'contractNom'=> $contractNom,
                    'login' => $auth->getUser()['login']]);
            throw new AppException('не найден договор');
        }

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = (new ContractInfoServiceInterface($this->pdo))->getContractInfo($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });
        usort($connectionsList, function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });

        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList) {
            while ($abonent['nab'] === (current($connectionsList)['nab'] ?? '')) {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });
        // проверить, есть ли сохраненные показания
        $readingsService = new ReadingsService($this->pdo, $auth->getUser(), $this->logger);
        $savedReadings = $readingsService->loadPeriodReadings(new DateTime('now'));
        if($savedReadings) {
            $isSavedReadings = false;
        } else {
            $savedReadings = $readingsService->loadTmpReadings();
            $isSavedReadings = true;
        }

        return $this->view->render(
            $this->response,
            'front/connection/readings.twig',
            [
                'contract' => $contract,
                'abonentsList' => $abonentsList,
                'readings' => $savedReadings ?? [],
                'isSavedReadings' => $isSavedReadings,
            ]
        );
    }

}