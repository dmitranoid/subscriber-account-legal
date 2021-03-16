<?php
declare(strict_types=1);


namespace App\Application\Service\Auth;


use App\Application\DTO\User\UserDto;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use App\Infrastructure\Service\Session\SessionInterface;
use PDO;
use Psr\Log\LoggerInterface;

class UserAuthService
{
    private PDO $pdo;

    private SessionInterface $session;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, PDO $pdo, SessionInterface $session)
    {
        $this->pdo = $pdo;
        $this->session = $session;
        $this->logger = $logger;
    }

    public function isUserAuthenticated(): bool
    {
        $user = $this->session->get('user');
        return !empty($user);
    }

    public function isCustomer()
    {
        /** @var UserDto $user */
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        return ('customer' == ($user->code_group ?? ''));
    }

    public function getUser(): UserDto
    {
        return $this->session->get('user');
    }

    public function isManager()
    {
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        return ('manager' == ($user->code_group ?? ''));
    }

    public function isAdmin()
    {
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        return ('admin' == ($user->code_group ?? ''));
    }

    public function doLogin(string $region, string $login, string $password): bool
    {
        $userRepository = new SqlUserRepository($this->pdo);
        $user = $userRepository->getByLoginForRegion($region, $login);
        if (empty($user)) {
            $this->logger->alert('login not found', ['region' => $region, 'login' => $login]);
            return false;
        }
        if (password_verify($password, $user->password)) {
            $this->logger->info('user logged in', ['region' => $user->region, 'login' => $user->login]);
            $this->saveToSession($user);
            return true;
        }
        $this->logger->alert('login password error', ['region' => $region, 'login' => $login]);
        return false;
    }

    private function saveToSession($user)
    {
        if (empty($user)) {
            $this->session->destroy();
        } else {
            $this->session->set('user', $user);
        }
    }

    public function doLogout()
    {
        $user = $this->getUser();
        $login = $user->login ?? 'EMPTY_USER';
        $region = $user->region ?? 'EMPTY_REGION';
        $this->logger->info('user {login}({region}) logged out', ['region' => $region, 'login' => $login]);
        $this->saveToSession(null);
    }
}