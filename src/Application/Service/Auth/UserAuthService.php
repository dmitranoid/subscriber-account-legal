<?php
declare(strict_types=1);


namespace App\Application\Service\Auth;


use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;
use Psr\Log\LoggerInterface;

class UserAuthService
{
    private \PDO $pdo;

    private array $session;

    private LoggerInterface $logger;

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
            // todo нужно чтобы работал один unset
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
        return ('manager' == $this->session['user']['code_group'] ?? '');
    }

    public function isAdmin()
    {
        return ('admin' == $this->session['user']['code_group'] ?? '');
    }


    public function doLogin(string $region, string $login, string $password): bool
    {
        $userRepository = new SqlUserRepository($this->pdo);
        $user = $userRepository->getByLoginForRegion($region, $login);
        if (empty($user)) {
            $this->logger->alert('login {region}-{login} not found', ['region'=>$region, 'login' => $login]);
            return false;
        }
        if (password_verify($password, $user['password'])) {
            $this->logger->info('user {region}-{login} logged in',['region'=>$user['region'], 'login' => $user['login']]);
            $this->saveToSession($user);
            return true;
        }
        $this->logger->alert('login {region}-{login} password error', ['region'=>$region, 'login' => $login]);
        return false;
    }

    public function doLogout()
    {
        $login = $this->session['user']['login'] ?? 'EMPTY_USER';
        $region = $this->session['user']['region'] ?? 'EMPTY_REGION';
        $this->logger->info('user {region}-{login} logged out', ['region'=> $region, 'login' => $login]);
        $this->saveToSession(null);
    }
}