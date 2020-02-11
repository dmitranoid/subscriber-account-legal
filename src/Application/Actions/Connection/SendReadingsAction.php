<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractInfoService;
use App\Application\Service\Readings\ReadingsService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class SendReadingsAction extends Action
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
        $readings = $this->request->getParsedBody();

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);

        $readingsService = new ReadingsService($this->pdo, $contract);
        $readingsService->publishReadings($readings);
        return $this->respondWithData(['message' => 'данные приняты на обработку']);
    }
}
