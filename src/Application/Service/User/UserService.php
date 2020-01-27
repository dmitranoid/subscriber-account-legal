<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Application\Exceptions\AppDbException;
use App\Application\Exceptions\AppException;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;

class UserService
{
    /**
     * @var PDO
     */
    private $pdo;

    /** @var SqlUserRepository */
    private $userRepository;

    public function __construct(SqlUserRepository $userRepository)
    {

        $this->userRepository = $userRepository;
    }


    public function getUsersList()
    {
        
    }

    /**
     * @param array $data
     * @throws AppException
     */
    public function add(array $data)
    {
        // todo validation
        try {
            $this->userRepository->insert($data);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    public function edit(array $data)
    {
        // todo validation
        try {
            $this->userRepository->update($data);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }


}