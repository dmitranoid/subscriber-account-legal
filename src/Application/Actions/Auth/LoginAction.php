<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Middleware\SessionMiddleware;
use App\Application\Service\Auth\UserAuthService;
use App\Domain\Contract\ContractNotFoundException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class LoginAction extends AuthAction
{
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';
        if ((empty($login) || empty($password))) {
            return $this->response->withHeader('Location', '/login');
        }
        $authService = new UserAuthService(
            $this->logger,
            $this->pdo,
            $this->request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE, [])
        );
        if (!$authService->doLogin($login, $password)) {
            return $this->response->withHeader('Location', '/login');
        }

//        $contract = (new SqlContractRepository($this->pdo))->getById($contractNom);
//        if (empty($contract)) {
//            return $this->response->withHeader('Location', '/login');
//        }
//        $_SESSION['contract'] = $contractNom;
        $redirectUrl = '/';
        if ($authService->isCustomer()) {
            $redirectUrl = '/contract';
        }
        if ($authService->isManager()) {
            $redirectUrl = '/admin/users';
        }

        return $this->response->withHeader('Location', $redirectUrl);
    }

}