<?php
declare(strict_types=1);


namespace App\Application\Service\Auth;


use App\Domain\Authentication\Account\Account;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Service\Session\SessionInterface;
use Psr\Log\LoggerInterface;

class AuthService
{
    private AccountRepositoryInterface $accountRepository;

    private SessionInterface $session;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, AccountRepositoryInterface $accountRepository, SessionInterface $session)
    {
        $this->session = $session;
        $this->logger = $logger;
        $this->accountRepository = $accountRepository;
    }

    public function isAccountAuthenticated(): bool
    {
        return ($this->getStoredAccountId() !== null);
    }

    public function getCurrentAccount(): Account
    {
        return $this->accountRepository->findById($this->getStoredAccountId());
    }

    private function getStoredAccountId(): ?int
    {
        return $this->session->get('accountId');
    }

    public function doLogin(string $region, string $login, string $password): bool
    {
        $account = $this->accountRepository->findByLoginForRegion($region, $login);
        if (null === $account) {
            $this->logger->alert('login not found', ['region' => $region, 'login' => $login]);
            return false;
        }
        if (false === password_verify($password, $account->getPassword())) {
            $this->logger->alert('login password error', ['region' => $region, 'login' => $login]);
            return false;
        }
        $this->logger->info('account logged in', ['region' => $account->getRegion(), 'login' => $account->getLogin()]);
        $this->saveToSession($account->getId());
        return true;
    }

    private function saveToSession(int $accountId): void
    {
        $this->session->set('accountId', $accountId);
    }

    private function clearSession(): void
    {
        $this->session->set('accountId', null);
    }

    public function doLogout(): void
    {
        $account = $this->getCurrentAccount();

        $login = $account->getLogin() ?? 'EMPTY_USER';
        $region = $account->getRegion() ?? 'EMPTY_REGION';
        $this->logger->info('user {login}({region}) logged out', ['region' => $region, 'login' => $login]);
        $this->clearSession();
    }
}