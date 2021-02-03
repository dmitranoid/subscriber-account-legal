<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Abonent\AbonentServiceInterface;
use App\Application\Service\Connection\ConnectionHistoryService;
use App\Application\Service\Connection\ConnectionService;
use App\Application\Service\Connection\ConnectionServiceInterface;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;

class ViewConnectionAction extends Action
{
    private function extractAbTu($str): array
    {
        $delimPos = mb_strpos($str, '-');
        $ab = mb_substr($str, 0, $delimPos);
        $tu = mb_substr($str, $delimPos + 1, mb_strlen($str) - $delimPos);
        $tu = str_replace('dot', '.', $tu);
        $tu = str_replace('slashr', '/', $tu);
        $tu = str_replace('slashl', '\\', $tu);
        return [
            $ab,
            $tu
        ];
    }

    protected function action(): Response
    {
        $contractNom = $_SESSION['user']['code_contract'] ?? null;
        // историю показаний можно собрать только из архивной базы
        $archPdo = new PDO('sqlite:' . dirname($_SERVER['DOCUMENT_ROOT']) . '/../data/sqlite/legal-arch.sqlite3'))

        list($abonentNom, $tuName) = $this->extractAbTu($this->request->getAttribute('abtu', ''));

        $connectionHistory = new ConnectionHistoryService($archPdo);
        $connectionService = AppHelper::container()->get(ConnectionServiceInterface::class);

        $contract = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNom);
        $abonent = AppHelper::container()->get(AbonentServiceInterface::class)->getByNom($contractNom, $abonentNom);
        $connection = $connectionService->getConnectionInfo($contractNom, $abonentNom, $tuName);
        $readingsHistory = $connectionHistory->getReadingsHistory($contractNom, $abonentNom, $tuName);

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