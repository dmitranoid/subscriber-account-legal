<?php
declare(strict_types=1);


namespace App\Application\Actions\Connection;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Readings\ReadingsService;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;

class SaveReadingsAction extends Action
{
    protected function action(): Response
    {
        $session = $this->request->getAttribute('session', []);
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $readings = $this->request->getParsedBody();

        $readingsService = new ReadingsService($this->pdo, $auth->getUser(), $this->logger);
        try {
            $readingsService->saveTmpReadings($readings);
        } catch (AppException $e) {
            $this->logger->error($e->getMessage());
            return $this->respondWithData(['error' => 'ошибка сохранения данных']);
        }
        return $this->respondWithData(['message' => 'данные сохранены']);
    }
}
