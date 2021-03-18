<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewConnectionsListAction extends Action
{
    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNom = $auth->getCurrentAccount()->getContractNo();

        /** @var ConnectionServiceInterface $connectionService */
        $connectionService = AppHelper::container()->get(ConnectionServiceInterface::class);
        /** @var AbonentServiceInterface $abonentService */
        $abonentService = AppHelper::container()->get(AbonentServiceInterface::class);
        /** @var ContractInfoServiceInterface $contractService */
        $contractService = AppHelper::container()->get(ContractInfoServiceInterface::class);

        $contract = $contractService->getContractInfo($contractNom);
        $connectionsList = $connectionService->getByContract($contractNom);
        $abonentsList = $abonentService->getByContract($contractNom);

//        array_walk($connectionsList, function (&$item) {
//            $tuUrl = $item['naim'];
//            $tuUrl = str_replace(['.', '/', '\\'], ['dot', 'slashr', 'slashl'], $tuUrl);
//            $item['naim_url'] = $tuUrl;
//        });

        /* combine abonents and connections */
        usort($abonentsList, function ($a, $b) {
            return $a->nom - $b->nom;
        });
        usort($connectionsList, function ($a, $b) {
            return $a->abonentNo - $b->abonentNo;
        });

        reset($connectionsList);
        array_walk($abonentsList, function (&$abonent) use (&$connectionsList) {
            while ($abonent->nom === (current($connectionsList)->abonentNo ?? '')) {
                $abonent['connections'][] = array_shift($connectionsList);
            }
        });
        return $this->view->render($this->response, 'front/connection/connections.twig', ['contract' => $contract, 'abonentsList' => $abonentsList]);
    }

}