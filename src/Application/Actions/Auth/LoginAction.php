<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Application\Helpers\AppHelper;
use App\Application\Helpers\UriHelper;
use App\Application\Service\Auth\AuthService;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Domain\Contract\ContractNotFoundException;
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

        $accountRepository = AppHelper::container()->get(AccountRepositoryInterface::class);
        $authService = new AuthService($this->logger, $accountRepository, new Session());

        if (!$authService->doLogin($region, $login, $password)) {
            $this->setFormOldData(['login' => $login, 'region' => $region]);
            $this->setFormErrors(['login' => ['неправильные имя пользователя или пароль']]);
            return $this->withRedirect(UriHelper::urlFor('login'));
        }
        $account = $authService->getCurrentAccount();
        $redirectUrl = '/';
        $cookies = new Cookies($this->request->getCookieParams());
        $cookies->set('login', $login);
        $cookies->set('region', $region);

        if ($account->isCustomer()) {
            $redirectUrl = UriHelper::urlFor('front.contract');
        }
        if ($account->isManager()) {
            $redirectUrl = UriHelper::urlFor('admin.users');
        }

        return $this->withRedirect($redirectUrl)->withHeader('Set-Cookie', $cookies->toHeaders());
    }

}