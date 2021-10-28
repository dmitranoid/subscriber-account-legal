<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Account;


use App\Domain\Authentication\Account\Account;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Persistence\GenericSqlRepository;
use Exception;
use Webmozart\Assert\Assert;

class SqlAccountRepository extends GenericSqlRepository implements AccountRepositoryInterface
{
    protected string $tableName = 'user';

    public function findByLoginForRegion(string $region, string $login): ?Account
    {
        try {
            $data = $this->fpdo
                ->from($this->tableName)
                ->where('login', $login)
                ->where('region', $region)
                ->fetch();
        } catch (Exception $e) {
            throw new Exception('db error', 0, $e);
        }
        //return $this->mapper->map($data);
        return (false === $data) ? null : $this->map($data);
    }

    public function findById(int $id): ?Account
    {
        $data = $this->getById($id);
        //return $this->mapper->map($data);
        return (false === $data) ? null : $this->map($data);
    }

    /**
     * @return Account[]|null
     */
    public function findAll(): array
    {
        $dataItems = $this->getAll();
        $accounts = [];
        foreach ($dataItems as $item) {
            // $accounts = $this->mapper->map($item);
            $accounts = $this->map($item);
        }
        return $accounts;
    }

    /**
     * @return Account[]
     * @throws \Envms\FluentPDO\Exception
     */
    public function getCustomers(): ?array
    {
        return $this->getAllSql()
            ->where('code_group', 'customer')
            ->fetchAll();
    }

    public function getUsers(): ?array
    {
        return $this->getAllSql()
            ->where('code_group <> ?', 'customer')
            ->fetchAll();
    }

    public function getCustomerByLogin($login): ?Account
    {
        $data = $this->getAllSql()
            ->where('code_group', 'customer')
            ->where('login', $login)
            ->fetch();
        //return $this->accountMapper->map($data);
        return (false === $data) ? null : $this->map($data);
    }

    private function map($data): Account
    {
        Assert::isArray($data);
        return new Account(
            (int)$data['id'],
            $data['login'],
            $data['password'],
            $data['code_group'],
            $data['code_contract'],
            $data['email'],
            $data['phones'],
            $data['region'],
        );
    }
}