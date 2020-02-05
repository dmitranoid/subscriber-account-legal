<?php
declare(strict_types=1);

namespace App\Application\Validators\User;


use App\Application\Validators\ValidatorInterface;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;

class UserExists implements ValidatorInterface
{

    /**
     * @var SqlUserRepository
     */
    private $userRepository;
    private $login;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new SqlUserRepository($pdo);
    }

    public function setData($login): void
    {
        $this->login = $login;
    }

    public function validate(): bool
    {
        $user = $this->userRepository->getByLogin($this->login);
        return !(empty($user) || false === $user);
    }
}