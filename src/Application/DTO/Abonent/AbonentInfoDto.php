<?php
declare(strict_types=1);


namespace App\Application\DTO\Abonent;


class AbonentInfoDto
{
    public string $contractNo;
    public string $abonentNo;
    public string $name;
    public string $address;

    public function __construct(string $contractNo, string $abonentNo, string $name, string $address)
    {
        $this->contractNo = $contractNo;
        $this->abonentNo = $abonentNo;
        $this->name = $name;
        $this->address = $address;
    }
}