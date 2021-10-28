<?php
declare(strict_types=1);

namespace App\Domain\Authentication\Account;


interface AccountRepositoryInterface
{
    /**
     * @return Account[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Account;

    public function findByLoginForRegion(string $region, string $login): ?Account;
}
