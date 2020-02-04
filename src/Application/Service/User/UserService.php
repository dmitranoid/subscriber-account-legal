<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Application\Exceptions\AppDbException;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\ArrayHelper;
use App\Application\Validators\Contract\ContractExists;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;

class UserService
{
    /** @var SqlUserRepository */
    private $userRepository;

    private $allowedFields = [
        'login',
        'password',
        'code_group',
        'code_contract',
        'email',
    ];
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userRepository = new SqlUserRepository($this->pdo);
    }

    public function getUsersList():array
    {

    }

    /**
     * @param array $data
     * @throws AppException
     */
    public function add(array $data):void
    {
        $data = ArrayHelper::filterArrayKeys($data, $this->allowedFields);
        // проверяем существует ли договор
        //todo инжектить котракт репозиторий или вынести проверку в валидатор
        $contractExistsValidator = new ContractExists($this->pdo);
        $contractExistsValidator->setData($data['code_contract']);
        if (!$contractExistsValidator->validate()) {
            throw new AppException(sprintf('договор %s не найден', $data['code_contract']));
        }
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        try {
            $this->userRepository->save($data);
        } catch (AppDbException $e) {
            throw new AppException('ошибка сохранения данных', 0, $e);
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
        //TODO проверить права пользователя, нельзя удалить с правами выше чем у себя
        $user = $this->userRepository->getByLogin($login);
        $this->delete($user['id']);
    }
}