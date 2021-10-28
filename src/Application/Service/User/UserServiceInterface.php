<?php

namespace App\Application\Service\User;

use App\Application\DTO\Account\AccountDto;
use App\Application\Exceptions\AppException;
use App\Infrastructure\ReadModel\PagerSettings;
use Exception;

interface UserServiceInterface
{
    /**
     * @param string $login
     * @throws AppException
     * @throws Exception
     */
    public function getByLoginAndRegion(string $login, string $region): ?AccountDto;

    /**
     * @return AccountDto[]
     */
    public function getAllUsersList(PagerSettings $pager): array;

    /**
     * @return AccountDto[]
     */
    public function getCustomersList(PagerSettings $pager): array;

    /**
     * @return AccountDto[]
     */
    public function getManagersList(PagerSettings $pager): array;

    /**
     * @return AccountDto[]
     */
    public function getAdminsList(PagerSettings $pager): array;

    /**
     * @param array $data
     * @throws AppException
     */
    public function addCustomer(array $data): void;

    /**
     * @param array $data
     * @throws AppException
     */
    public function addManager(array $data): void;

    /**
     * @param array $data
     * @throws AppException
     */
    public function update(array $data): void;

    /**
     * @param int $id
     * @throws AppException
     */
    public function delete(int $id);

    public function deleteByLoginAndRegion($login, $region);
}