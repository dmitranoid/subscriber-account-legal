<?php
declare(strict_types=1);


namespace App\Application\DTO\Finance;


class DebtDto
{
    public string $contractNo;
    public \DateTimeImmutable $date;
    public float $sum;

    public function __construct(string $contractNo, \DateTimeImmutable $date, float $sum)
    {
        $this->contractNo = $contractNo;
        $this->sum = $sum;
        $this->date = $date;
    }
}