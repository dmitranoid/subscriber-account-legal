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
        $authService = new UserAuthService($this->logger, $this->pdo, new Session());
        if ($authService->isUserAuthenticated()) {
            return $this->response->withHeader('Location', '/contract');
        }

        $regions = [
            'bar' => 'Барановичские электрические сети',
            'bre' => 'Брестские электрические сети',
            'pin' => 'Пинские электрические сети',
            'pru' => 'Пружанские электрические сети',
        ];
        return $this->view->render($this->response, 'front/auth/login.twig', ['regions'=>$regions]);
    }

}