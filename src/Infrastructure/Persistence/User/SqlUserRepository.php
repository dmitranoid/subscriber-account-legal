<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\User;


use App\Application\Exceptions\AppDbException;
use App\Infrastructure\Persistence\GenericSqlRepository;
use Envms\FluentPDO\Exception as FpdoException;
use Exception;

class SqlUserRepository extends GenericSqlRepository
{
    protected $tableName = 'user';

    public function getByLogin($login): ?array
    {
        try {
            $user = $this->fpdo
                ->from($this->tableName)
                ->where('login', $login)
                ->fetch();
        } catch (Exception $e) {
            new Exception('internal error', 0, $e);
        }
        return $user ? $user : null;
    }

    public function getCustomers(): ?array
    {
        return $this->getAllSql()
            ->where('code_group', 'customer')
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