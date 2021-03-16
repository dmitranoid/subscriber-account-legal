<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Service\Auth\UserAuthService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Cookies;

class LogoutAction extends AuthAction
{

    protected function action(): Response
    {
        $authService = new UserAuthService(
            $this->logger,
            $this->pdo,
            new Session()
        );

        $cookies = new Cookies($this->request->getCookieParams());
        $cookies->set('login', $authService->getUser());
        $cookies->set('region', $authService->getUser());

        $authService->doLogout();

        return $this->response->withHeader('Location', '/login');
    }
}