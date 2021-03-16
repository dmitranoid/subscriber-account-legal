<?php
declare(strict_types=1);

namespace App\Domain\User;


class User
{

    private int $id;

    private string $login;

    private string $password;

    private string $code_group;

    private string $code_contract;

    private string $email;

    private string $phones;

    private string $region;

    public function getId(): ?int
    {
        return $this->id;
    }

}
