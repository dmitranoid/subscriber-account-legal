<?php
declare(strict_types=1);

namespace App\Application\DTO\Connection;


use DateTime;

class CounterDto
{
    public string $model;
    public string $serialNo;
    public string $coeffTr;
    public ?\DateTimeImmutable $installDate;
    /** @var \DateTimeImmutable|null дата поверки */
    public ?\DateTimeImmutable $checkoutDate;
    /** @var float|null последнее показание */
    public ?float $lastValue;
    /** @var float|null последний расход */
    public ?float $lastExpense;

    public function __construct(string $model, string $serialNo, string $coeffTr, ?\DateTimeImmutable $installDate, ?\DateTimeImmutable $checkoutDate, ?float $lastValue, ?float $lastExpense)
    {
        $this->model = $model;
        $this->serialNo = $serialNo;
        $this->coeffTr = $coeffTr;
        $this->installDate = $installDate;
        $this->checkoutDate = $checkoutDate;
        $this->lastValue = $lastValue;
        $this->lastExpense = $lastExpense;
    }

}