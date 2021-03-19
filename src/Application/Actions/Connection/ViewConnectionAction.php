<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewConnectionAction extends Action
{
    private function extractAbTu($str): array
    {
        $delimPos = mb_strpos($str, '-');
        $ab = mb_substr($str, 0, $delimPos);
        $tu = mb_substr($str, $delimPos + 1, mb_strlen($str) - $delimPos);
        $tu = str_replace(array('dot', 'slashr', 'slashl'), array('.', '/', '\\'), $tu);
        return [
            $ab,
            $tu
        ];
    }

    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        list($abonentNom, $tuName) = $this->extractAbTu($this->request->getAttribute('abtu', ''));

        $connectionService = AppHelper::container()->get(ConnectionServiceInterface::class);

        $contract = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNo);
        $abonent = AppHelper::container()->get(AbonentServiceInterface::class)->getAbonentInfo($contractNo, $abonentNom);
        $connection = $connectionService->getConnectionInfo($contractNo, $abonentNom, $tuName);
        $readingsHistory = $connectionService->getReadingsHistory($contractNo, $abonentNom, $tuName);

        return $this->view->render(
            $this->response,
            'front/connection/connection.twig',
            [
                'contract' => $contract,
                'abonent' => $abonent,
                'connection' => $connection,
                'readingsHistory' => $readingsHistory,
            ]
        );
    }
}