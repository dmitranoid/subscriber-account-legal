<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\User;


use App\Application\Exceptions\AppDbException;
use App\Infrastructure\Persistence\GenericSqlRepository;
use Envms\FluentPDO\Exception as FpdoException;
use Exception;

class SqlUserRepository extends GenericSqlRepository
{
    protected string $tableName = 'user';

    public function getByLoginForRegion($region, $login): ?array
    {
        try {
            $user = $this->fpdo
                ->from($this->tableName)
                ->where('login', $login)
                ->where('region', $region)
                ->fetch();
        } catch (Exception $e) {
            new Exception('db error', 0, $e);
        }
        return $user ? $user : null;
    }

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


    public function getCustomerByLogin($login): ?array
    {
        return $this->getAllSql()
            ->where('code_group', 'customer')
            ->where('login', $login)
            ->fetch();
    }

}