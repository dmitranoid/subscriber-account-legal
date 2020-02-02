<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Application\Exceptions\AppDbException;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\ArrayHelpers;
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

    private $allowedFields = [
        'login',
        'password',
        'code_group',
        'code_contract',
        'email',
    ];

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
        $data = ArrayHelpers::filterArrayKeys($data, $this->allowedFields);

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        try {
            $this->userRepository->save($data);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    /**
     * @param array $data
     * @throws AppException
     */
    public function edit(array $data)
    {
        // todo validation
        try {
            $this->userRepository->update($data);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    /**
     * @param $id
     * @throws AppException
     */
    public function delete($id)
    {
        try {
            $this->userRepository->delete($id);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    public function deleteByLogin($login)
    {
        $user = $this->userRepository->getByLogin($login);
        $this->delete($user['id']);
    }
}