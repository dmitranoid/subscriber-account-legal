<?php
declare(strict_types=1);


namespace App\Infrastructure\ReadModel\User;


use App\Infrastructure\ReadModel\GenericSqlFinder;
use App\Infrastructure\ReadModel\PagerSettings;

class UserFinder extends GenericSqlFinder
{
    protected string $tableName = 'user';
    protected $identity = 'id';

    public function getCustomers(array $filter = [], PagerSettings $pager = null): array
    {
        $fpdo = $this->getAllSql();
        $fpdo->where('code_group', 'customer');
        $fpdo = $this->applyPager($fpdo, $pager);
        $fpdo = $this->applyFilter($fpdo, $filter);
        return $fpdo->fetchAll();
    }

    public function getManagers(array $filter, PagerSettings $pager): array
    {
        $fpdo = $this->getAllSql();
        $fpdo->where('code_group', 'manager');
        $fpdo = $this->applyPager($fpdo, $pager);
        $fpdo = $this->applyFilter($fpdo, $filter);
        return $fpdo->fetchAll();
    }

    public function findByLoginForRegion($login, $region): ?array
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
        return (false === $data) ? null : $data;
    }
}