<?php
declare(strict_types=1);

namespace App\Application\DTO\Contract;


use \DateTime;

final class ContractInfo
{
    public string $nom;
    public string $nomAdd;
    public DateTime $date;
    public string $name;
    public string $fio;
    public string $phone;
    public string $bank;
    public string $payBank;
    public string $managedByUser;

    public function __construct($nom, $nomAdd, $date, $name, $fio, $phone, $bank, $payBank, $managedByUser)
    {
        $this->nom = $nom;
        $this->nomAdd = $nomAdd;
        $this->date = $date;
        $this->name = $name;
        $this->fio = $fio;
        $this->phone = $phone;
        $this->bank = $bank;
        $this->payBank = $payBank;
        $this->managedByUser = $managedByUser;
    }
}