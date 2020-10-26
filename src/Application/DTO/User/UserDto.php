<?php
declare(strict_types=1);

namespace App\Application\DTO\User;


class UserDto
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
}