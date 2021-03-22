<?php
declare(strict_types=1);

namespace App\Application\DTO\Connection;


class ConnectionDto
{
    public string $contractNo;
    public string $abonentNo;
    public string $name;
    public string $calcMode; // Способ расчетов
    public string $tariffName;
    public ?CounterDto $counter;

    public function __construct(string $contractNo, string $abonentNo, string $name, string $calcMode, string $tariffName, ?CounterDto $counter)
    {
        $this->contractNo = $contractNo;
        $this->abonentNo = $abonentNo;
        $this->name = $name;
        $this->calcMode = $calcMode;
        $this->tariffName = $tariffName;
        $this->counter = $counter;
    }


}