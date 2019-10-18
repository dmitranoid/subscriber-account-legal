<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\FoxproAbonentRepository;
use App\Infrastructure\Persistence\Connection\FoxproConnectionRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class SendReadingsAction extends ContractAction
{
    protected function action(): Response
    {
        $contractNom = $_SESSION['contract'] ?? null;

        $readings = $this->request->getParsedBody();

        $contractRepository = new FoxproContractRepository();
        $connectionsRepository = new FoxproConnectionRepository();
        $abonentsRepository = new FoxproAbonentRepository();

        $contract = $contractRepository->getByNomArr($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        $filename = sprintf("%s-%s.txt", $contractNom, date('Ymd'));
        $dir = $_SERVER['DOCUMENT_ROOT'] . sprintf("/../storage/readings/%s/", date('Ym'));
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        file_put_contents($dir . $filename, json_encode($readings));
        return $this->respondWithData(['message' => 'данные приняты на обработку']);
    }
}
