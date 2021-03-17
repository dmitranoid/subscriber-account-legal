<?php
declare(strict_types=1);

namespace App\Domain\Authentication\Account;


interface AccountRepository
{
    /**
     * @return Account[]
     */
    public function findAll(): array;

    public function getById(int $id): Account;

    public function getByLoginForRegion($region, $login): Account;
}
