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

    /**
     * UserDto constructor.
     * @param string $id
     * @param string $login
     * @param string $passwordHash
     * @param string $code_group
     * @param string $code_contract
     * @param string $statuses
     * @param string $phones
     * @param string $email
     * @param string $region
     */
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