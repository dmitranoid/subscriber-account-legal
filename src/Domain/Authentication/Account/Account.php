<?php
declare(strict_types=1);

namespace App\Domain\Authentication\Account;


class Account
{

    private ?int $id;

    private string $login;

    private string $password;

    private string $group;

    private ?string $contractNo;

    private string $email;

    private ?string $phones;

    private string $region;

    public function __construct(?int $id, string $login, string $password, string $group, string $contractNo, string $email, ?string $phones, string $region)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->group = $group;
        $this->contractNo = $contractNo;
        $this->email = $email;
        $this->phones = $phones;
        $this->region = $region;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function getContractNo(): string
    {
        return $this->contractNo;
    }

    public function setContractNo(string $contractNo): void
    {
        $this->contractNo = $contractNo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhones(): string
    {
        return $this->phones;
    }

    public function setPhones(string $phones): void
    {
        $this->phones = $phones;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }


    public function isAdmin(): bool
    {
        return ('admin' === ($this->group));
    }

    public function isManager(): bool
    {
        return ('manager' === ($this->group));
    }

    public function isCustomer(): bool
    {
        return ('customer' === ($this->group));
    }

}
