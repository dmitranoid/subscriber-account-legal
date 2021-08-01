<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use App\Application\DTO\Account\AccountDto;
use App\Application\Exceptions\AppDbException;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\ArrayHelper;
use App\Application\Validators\Contract\ContractExists;
use App\Application\Validators\User\UserExists;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Persistence\Account\SqlAccountRepository;
use PDO;
use Psr\Log\LoggerInterface;

class UserService
{
    private AccountRepositoryInterface $accountRepository;

    private $allowedFields = [
        'login',
        'password',
        'code_group',
        'code_contract',
        'email',
        'phones'
    ];

    private PDO $pdo;

    private LoggerInterface $logger;

    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
        $this->pdo = $pdo;
        $this->accountRepository = new SqlAccountRepository($this->pdo);
        $this->logger = $logger;
    }

    /**
     * @param string $login
     * @return array
     * @throws AppException
     * @throws \Exception
     */
    public function getByLoginAndRegion(string $login, string $region): AccountDto
    {
        $account = $this->accountRepository->findByLoginForRegion($login, $region);
        if (is_null($account)) {
            throw new AppException(sprintf('пользователь %s не существует в регионе %s', $login, $region));
        }
        return $account;
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
            $this->accountRepository->save($data);
        } catch (AppDbException $e) {
            throw new AppException('ошибка сохранения данных', 0, $e);
        }
    }

    public function addManager(array $data):void
    {
        $data = ArrayHelper::filterArrayKeys($data, $this->allowedFields);
        // проверяем существует ли договор
        //todo инжектить контракт репозиторий или вынести проверку в валидатор
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
            $this->accountRepository->save($data);
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
            $user = $this->getByLoginAndRegion($data['login'], $data['region']);
            $data['id'] = $user['id'];
        }
        try {
            $this->accountRepository->update($data);
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
            $this->accountRepository->delete($id);
        } catch (AppDbException $e) {
            throw new AppException();
        }
    }

    public function deleteByLoginAndRegion($login, $region)
    {
        //TODO проверить права пользователя
        $user = $this->accountRepository->findByLoginForRegion($login, $region);
        $this->delete((int)$user['id']);
    }
}