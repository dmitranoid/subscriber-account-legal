<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Readings\ReadingsService;
use App\Infrastructure\Persistence\Abonent\SqlAbonentRepository;
use App\Infrastructure\Persistence\Connection\SqlConnectionRepository;
use Psr\Http\Message\ResponseInterface as Response;

class SendReadingsAction extends Action
{
    protected function action(): Response
    {
        $session = $this->request->getAttribute('session', []);
        $auth = new AuthService($this->logger, $this->pdo, $session);
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $readings = $this->request->getParsedBody();

        $connectionsRepository = new SqlConnectionRepository($this->pdo);
        $abonentsRepository = new SqlAbonentRepository($this->pdo);

        $contract = (new ContractInfoServiceInterface($this->pdo))->getContractInfo($contractNom);
        $abonentsList = $abonentsRepository->getByContract($contractNom);
        $connectionsList = $connectionsRepository->getByContract($contractNom);

        $readingsService = new ReadingsService($this->pdo, $auth->getUser());
        try {
            $readingsService->publishReadings($contract, $abonentsList, $connectionsList, $readings);
        } catch (AppException $e) {
            return $this->respondWithData(['message' => 'ошибка обработки данных, свяжитесь со своим менеджером']);
        }
        return $this->respondWithData(['message' => 'данные приняты на обработку']);
    }
}
