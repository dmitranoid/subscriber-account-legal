<?php
declare(strict_types=1);


namespace App\Application\DTO\Abonent;


class AbonentInfoDto
{
    public string $contractNom;
    public string $nom;
    public string $name;
    public string $address;

    public function __construct(string $contractNom, string $nom, string $name, string $address)
    {
        $this->contractNom = $contractNom;
        $this->nom = $nom;
        $this->name = $name;
        $this->address = $address;
    }
}