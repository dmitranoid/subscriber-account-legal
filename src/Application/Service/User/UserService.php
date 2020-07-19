<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Application\Exceptions\AppDbException;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\ArrayHelper;
use App\Application\Validators\Contract\ContractExists;
use App\Application\Validators\User\UserExists;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;
use Psr\Log\LoggerInterface;

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
        'phones'
    ];
    /** @var PDO */
    private $pdo;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->userRepository = new SqlUserRepository($this->pdo);
        $this->logger = $logger;
    }

    /**
     * @param string $login
     * @return array
     * @throws AppException
     */
    public function getByLogin(string $login):array
    {
        $user = $this->userRepository->getByLogin($login);
        if (false === $user || empty($user)) {
            throw new AppException(sprintf('пользователь %s не существует', $login));
        }
        return $user;
    }

    public function getUsersList():array
    {
        return [];
    }

    /**
     * @param array $data
     * @throws AppException
     */
    public function addCustomer(array $data):void
    {
        $data = ArrayHelper::filterArrayKeys($data, $this->allowedFields);
        // проверяем существует ли договор
        //todo инжектить котракт репозиторий или вынести проверку в валидатор
        $contractExistsValidator = new ContractExists($this->pdo);
        $contractExistsValidator->setData($data['code_contract']);
        if (!$contractExistsValidator->validate()) {
            $this->logger->info('попытка регистрации пользователя {login} с несуществующим договором {code_contract}', $data);
            throw new AppException(sprintf('договор %s не найден', $data['code_contract']));
        }
        $userExistsValidator = new UserExists($this->pdo);
        $userExistsValidator->setData($data['login']);
        if (!$userExistsValidator->validate()) {
            $this->logger->info('попытка повторно зарегистрировать пользователя {login}', $data);
            throw new AppException(sprintf('пользователь %s уже существует', $data['login']));
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

    public function addManager(array $data):void
    {
        $data = ArrayHelper::filterArrayKeys($data, $this->allowedFields);
        // проверяем существует ли договор
        //todo инжектить котракт репозиторий или вынести проверку в валидатор
        $userExistsValidator = new UserExists($this->pdo);
        $userExistsValidator->setData($data['login']);
        if (!$userExistsValidator->validate()) {
            $this->logger->info('попытка повторно зарегистрировать пользователя {login}', $data);
            throw new AppException(sprintf('пользователь %s уже существует', $data['login']));
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
    public function update(array $data): void
    {
        if (!isset($data['id'])) {
            $user = $this->getByLogin($data['login']);
            $data['id'] = $user['id'];
        }
        try {
            $this->userRepository->update($data);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    /**
     * @param int $id
     * @throws AppException
     */
    public function delete(int $id)
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
        $this->delete((int)$user['id']);
    }
}