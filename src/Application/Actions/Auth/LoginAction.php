<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Helpers\UriHelper;
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
        $form = $this->request->getParsedBody();
        $login = $form['login'] ?? '';
        $password = $form['password'] ?? '';
        if ((empty($login) || empty($password))) {
            return $this->response->withHeader('Location', '/login');
        }
        $authService = new UserAuthService(
            $this->logger,
            $this->pdo,
            $this->request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE, [])
        );
        if (!$authService->doLogin($login, $password)) {
            $this->setFormOldData(['login'=>$login]);
            $this->setFormErrors(['login'=>['неправильные имя пользователя или пароль']]);
            return $this->withRedirect(UriHelper::urlFor('login'));
        }

//        $contract = (new SqlContractRepository($this->pdo))->getById($contractNom);
//        if (empty($contract)) {
//            return $this->response->withHeader('Location', '/login');
//        }
//        $_SESSION['contract'] = $contractNom;
        $redirectUrl = '/';
        if ($authService->isCustomer()) {
            $redirectUrl = UriHelper::urlFor('front.contract');
        }
        if ($authService->isManager()) {
            $redirectUrl = UriHelper::urlFor('admin.users');
        }

        return $this->withRedirect($redirectUrl);
    }

}