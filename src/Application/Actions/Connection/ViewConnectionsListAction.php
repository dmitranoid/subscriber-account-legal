<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\DTO\Abonent\AbonentInfoDto;
use App\Application\DTO\Connection\ConnectionDto;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewConnectionsListAction extends Action
{
    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        /** @var ConnectionServiceInterface $connectionService */
        $connectionService = AppHelper::container()->get(ConnectionServiceInterface::class);
        /** @var AbonentServiceInterface $abonentService */
        $abonentService = AppHelper::container()->get(AbonentServiceInterface::class);
        /** @var ContractInfoServiceInterface $contractService */
        $contractService = AppHelper::container()->get(ContractInfoServiceInterface::class);

        $contractInfo = $contractService->getContractInfo($contractNo);
        $connectionsList = $connectionService->getByContract($contractNo);
        $abonentsList = $abonentService->getByContract($contractNo);

        // сортируем абонентов и ТУ по абонетскому номеру
        usort($abonentsList, function (AbonentInfoDto $a, AbonentInfoDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });
        usort($connectionsList, function (ConnectionDto $a, ConnectionDto $b) {
            return $a->abonentNo - $b->abonentNo;
        });

        return $this->view->render(
            $this->response,
            'front/connection/connections.twig',
            [
                'contractInfo' => $contractInfo,
                'abonentsList' => $abonentsList,
                'connectionsList' => $connectionsList,
            ]
        );
    }

}