<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\DTO\Abonent\AbonentInfoDto;
use App\Application\DTO\Connection\ConnectionDto;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Readings\ReadingsService;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;

class ViewListReadingsAction extends Action
{
    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        if (empty($contractNo)) {
            $this->logger->error(
                'не найден договор {contractNo}, пользователь {login}',
                [
                    'contractNo' => $contractNo,
                    'login' => $auth->getUser()['login']]);
            throw new AppException('не найден договор');
        }

        $contract = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNo);
        $connectionsList = AppHelper::container()->get(ConnectionServiceInterface::class)->getByContract($contractNo);
        $abonentsList = AppHelper::container()->get(AbonentServiceInterface::class)->getByContract($contractNo);

        // сортируем абонентов и ТУ по абонетскому номеру
        usort($abonentsList, function (AbonentInfoDto $a, AbonentInfoDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });
        usort($connectionsList, function (ConnectionDto $a, ConnectionDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });

        // проверить, есть ли сохраненные показания
        $readingsService = AppHelper::container()->get(ReadingsService::class);
        $readingsService->setCustomer($auth->getCurrentAccount());

        $savedReadings = $readingsService->loadPeriodReadings(new DateTime('now'));
        if ($savedReadings) {
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
                'connectionsList' => $connectionsList,
                'readings' => $savedReadings ?? [],
                'isSavedReadings' => $isSavedReadings,
            ]
        );
    }

}