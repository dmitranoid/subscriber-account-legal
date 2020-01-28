<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Service\Auth\UserAuthService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class LogoutAction extends AuthAction
{

    protected function action(): Response
    {
        $authService = new UserAuthService(
            $this->logger,
            $this->pdo,
            $this->request->getAttribute('session')
        );
        $authService->doLogout();
        return $this->response->withHeader('Location', '/login');
    }
}