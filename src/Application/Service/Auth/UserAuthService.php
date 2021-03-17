<?php
declare(strict_types=1);


namespace App\Application\Service\Auth;


use App\Application\DTO\User\UserDto;
use App\Domain\Authentication\Account\Account;
use App\Domain\Authentication\Account\AccountRepository;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use App\Infrastructure\Service\Session\SessionInterface;
use PDO;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Log\LoggerInterface;

class UserAuthService
{
    private AccountRepository $accountRepository;

    private SessionInterface $session;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, AccountRepository $accountRepository, SessionInterface $session)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->accountRepository = $accountRepository;
    }

    public function isUserAuthenticated(): bool
    {
        $user = $this->session->get('accountId');
        return !empty($user) && ($user instanceof Account);
    }

    public function getStoredAccountId(): Integer
    {
        return $this->session->get('accountId');
    }


    public function doLogin(string $region, string $login, string $password): bool
    {
        $account = $this->accountRepository->getByLoginForRegion($region, $login);
        if (empty($account)) {
            $this->logger->alert('login not found', ['region' => $region, 'login' => $login]);
            return false;
        }
        if (password_verify($password, $account->getPassword())) {
            $this->logger->info('account logged in', ['region' => $account->getRegion(), 'login' => $account->getLogin()]);
            $this->saveToSession($account->getId());
            return true;
        }
        $this->logger->alert('login password error', ['region' => $region, 'login' => $login]);
        return false;
    }

    private function saveToSession(int $accountId): void
    {
        $this->session->set('accountId', $accountId);
    }

    private function clearSession(): void
    {
        $this->session->set('accountId', null);
    }

    public function doLogout()
    {
        $user = $this->accountRepository->getById($this->getStoredAccountId());

        $login = $user->getLogin() ?? 'EMPTY_USER';
        $region = $user->getRegion() ?? 'EMPTY_REGION';
        $this->logger->info('user {login}({region}) logged out', ['region' => $region, 'login' => $login]);
        $this->clearSession();
    }
}