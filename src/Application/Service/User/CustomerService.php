<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;

class CustomerService
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCustomersList()
    {
        $userRepository = new SqlUserRepository($this->pdo);
        return $userRepository->getCustomers();
    }

    public function getCustomerByLogin($login)
    {
        $userRepository = new SqlUserRepository($this->pdo);
        return $userRepository->getCustomerByLogin($login);
    }

    public function getEmptyUser()
    {
        return [
            'login' => '',
            'code_group' => '',
            'code_contract' => '',
            'statuses' => '',
        ];
    }
}