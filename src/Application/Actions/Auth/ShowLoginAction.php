<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Service\Auth\UserAuthService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;


class ShowLoginAction extends AuthAction
{

    protected function action(): Response
    {
        $session = new Session();
        $cookies = $this->request->getCookieParams();
        $authService = new UserAuthService($this->logger, $this->pdo, $session);
        if ($authService->isUserAuthenticated()) {
            return $this->response->withHeader('Location', '/contract');
        }

        $regions = [
            'bar' => 'Барановичские электрические сети',
            'bre' => 'Брестские электрические сети',
            'pin' => 'Пинские электрические сети',
            'pru' => 'Пружанские электрические сети',
        ];

        $this->setFormOldData([
            'login' => $cookies['login'] ?? '',
            'region' => $cookies['region'] ?? ''
            ]
        );
        return $this->view->render($this->response, 'front/auth/login.twig', compact('regions'));
    }

}