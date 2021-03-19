<?php
declare(strict_types=1);

namespace App\Application\DTO\Contract;


use DateTime;

final class ContractDto
{
    public string $contractNo;
    public string $contractNoAdd;
    public DateTime $date;
    public string $name;
    public string $unp;
    public string $addressLegal;
    public string $fio;
    public string $phone;
    public string $bank;
    public string $payBank;
    public string $managedByUser;

    public function __construct($contractNo, $contractNoAdd, $date, $name, $unp, $addressLegal, $fio, $phone, $bank, $payBank, $managedByUser)
    {
        $this->contractNo = $contractNo;
        $this->contractNoAdd = $contractNoAdd;
        $this->date = $date;
        $this->name = $name;
        $this->unp = $unp;
        $this->fio = $fio;
        $this->phone = $phone;
        $this->bank = $bank;
        $this->payBank = $payBank;
        $this->managedByUser = $managedByUser;
        $this->addressLegal = $addressLegal;
    }
}