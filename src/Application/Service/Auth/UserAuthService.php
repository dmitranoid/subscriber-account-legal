<?php
declare(strict_types=1);


namespace App\Application\Service\Auth;


use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;
use Psr\Log\LoggerInterface;

class UserAuthService
{
    /**
     * @var PDO
     */
    private $pdo;

    private $session;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, PDO $pdo, $session)
    {
        $this->pdo = $pdo;
        $this->session = $session;
        $this->logger = $logger;
    }

    private function saveToSession($user)
    {
        // TODO переделать на DI session manager
        if (empty($user)) {
            unset($_SESSION['user']);
            unset($this->session['user']);
        } else {
            $_SESSION['user'] = $user;
            $this->session['user'] =$user;
        }
    }

    public function isUserAuthenticated(): bool
    {
        return isset($this->session['user']);
    }

    public function getUser(): array
    {
        return $this->session['user'] ?? [];
    }

    public function isCustomer()
    {
        return ('customer' == ($this->session['user']['code_group'] ?? ''));
    }

    public function isManager()
    {
        return (('manager' == $this->session['user']['code_group'] ?? '') ||
            ('admin' == $this->session['user']['code_group'] ?? ''));
    }

    public function doLogin(string $login, string $password): bool
    {
        $userRepository = new SqlUserRepository($this->pdo);
        $user = $userRepository->getByLogin($login);
        if (empty($user)) {
            $this->logger->alert('login {login} not found', ['login' => $login]);
            return false;
        }
        if (password_verify($password, $user['password'])) {
            $this->logger->info('user {login} logged in', ['login' => $login]);
            $this->saveToSession($user);
            return true;
        }
        $this->logger->alert('login {login} password error', ['login' => $login]);
        return false;
    }

    public function doLogout()
    {
        $login = $this->session['user']['login'] ?? 'EMPTY_USER';
        $this->logger->info('user {login} logged out', ['login' => $login]);
        $this->saveToSession(null);
    }
}