<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;

class ViewConnectionsListAction extends Action
{
    protected function action(): Response
    {
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;

        /** @var ConnectionServiceInterface $connectionService */
        $connectionService = AppHelper::container()->get(ConnectionServiceInterface::class);
        $abonentService = AppHelper::container()->get(AbonentServiceInterface::class);
        $contractService = AppHelper::container()->get(ContractInfoServiceInterface::class);

        $contract = $contractService->getContractInfo($contractNom);
        $connectionsList = $connectionService->getByContract($contractNom);
        $abonentsList = $abonentService->getByContract($contractNom);

        array_walk($connectionsList, function (&$item) {
            $tuUrl = $item['naim'];
            $tuUrl = str_replace(['.', '/', '\\'], ['dot', 'slashr', 'slashl'], $tuUrl);
            $item['naim_url'] = $tuUrl;
        });

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
        return $this->view->render($this->response, 'front/connection/connections.twig', ['contract' => $contract, 'abonentsList' => $abonentsList]);
    }

}