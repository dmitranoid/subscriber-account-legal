<?php
declare(strict_types=1);

namespace App\Application\DTO\Account;


class AccountDto
{
    public string $id;
    public string $login;
    public string $passwordHash;
    public string $code_group;
    public string $code_contract;
    public string $statuses;
    public string $phones;
    public string $email;
    public string $region;

    public function __construct(string $id, string $login, string $passwordHash, string $code_group, string $code_contract, string $statuses, string $phones, string $email, string $region)
    {
        $this->id = $id;
        $this->login = $login;
        $this->passwordHash = $passwordHash;
        $this->code_group = $code_group;
        $this->code_contract = $code_contract;
        $this->statuses = $statuses;
        $this->phones = $phones;
        $this->email = $email;
        $this->region = $region;
    }
}