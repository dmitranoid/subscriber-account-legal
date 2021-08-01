<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;


class ShowLoginAction extends AuthAction
{

    protected function action(): Response
    {
        $session = new Session();
        $cookies = $this->request->getCookieParams();
        $accountRepository = AppHelper::container()->get(AccountRepositoryInterface::class);
        $authService = new AuthService($this->logger, $accountRepository, $session);
        if ($authService->isAccountAuthenticated()) {
            return $this->response->withHeader('Location', '/contract');
        }

        $regions = [
            'Bares' => 'Барановичские электрические сети',
            'Brees' => 'Брестские электрические сети',
            'Pines' => 'Пинские электрические сети',
            'Prues' => 'Пружанские электрические сети',
        ];

        $this->setFormOldData([
            'login' => $cookies['login'] ?? '',
            'region' => $cookies['region'] ?? ''
            ]
        );
        return $this->view->render($this->response, 'front/auth/login.twig', compact('regions'));
    }

}