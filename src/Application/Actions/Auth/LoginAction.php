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
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Cookies;

class LoginAction extends AuthAction
{
    protected function action(): Response
    {
        $form = $this->request->getParsedBody();
        $region = $form['region'] ?? 'bar';
        $login = $form['login'] ?? '';
        $password = $form['password'] ?? '';
        if ((empty($login) || empty($password))) {
            return $this->withRedirect(UriHelper::urlFor('login'));
        }
        $authService = new UserAuthService(
            $this->logger,
            $this->pdo,
            new Session()
        );
        if (!$authService->doLogin($region, $login, $password)) {
            $this->setFormOldData(['login' => $login, 'region' => $region]);
            $this->setFormErrors(['login' => ['неправильные имя пользователя или пароль']]);
            return $this->withRedirect(UriHelper::urlFor('login'));
        }

        $redirectUrl = '/';
        $cookies = new Cookies($this->request->getCookieParams());
        $cookies->set('login', $login);
        $cookies->set('region', $region);

        if ($authService->isCustomer()) {
            $redirectUrl = UriHelper::urlFor('front.contract');
        }
        if ($authService->isManager()) {
            $redirectUrl = UriHelper::urlFor('admin.users');
        }

        return $this->withRedirect($redirectUrl)->withHeader('Set-Cookie', $cookies->toHeaders());
    }

}