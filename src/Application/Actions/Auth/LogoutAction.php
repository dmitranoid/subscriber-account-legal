<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Cookies;

class LogoutAction extends AuthAction
{

    protected function action(): Response
    {
        $accountRepository = AppHelper::container()->get(AccountRepositoryInterface::class);
        $authService = new AuthService($this->logger, $accountRepository, new Session());
        $account = $authService->getCurrentAccount();
        $cookies = new Cookies($this->request->getCookieParams());
        $cookies->set('login', $account->getLogin());
        $cookies->set('region', $account->getRegion());

        $authService->doLogout();

        return $this->response->withHeader('Location', '/login');
    }
}