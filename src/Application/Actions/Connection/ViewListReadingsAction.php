<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
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
        $auth = new AuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error(
                'не найден договор {contractNom}, пользователь {login}',
                [
                    'contractNom' => $contractNom,
                    'login' => $auth->getUser()['login']]);
            throw new AppException('не найден договор');
        }

        $contract = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNom);
        $connectionsList = AppHelper::container()->get(ConnectionServiceInterface::class)->getByContract($contractNom);
        $abonentsList = AppHelper::container()->get(AbonentServiceInterface::class)->getByContract($contractNom);

        /* combine abonents and connections */
        usort($abonentsList, static function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });
        usort($connectionsList, static function ($a, $b) {
            return $a['nab'] - $b['nab'];
        });

        reset($connectionsList);
        array_walk($abonentsList, static function (&$abonent) use (&$connectionsList) {
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