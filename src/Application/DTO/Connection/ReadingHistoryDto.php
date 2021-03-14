<?php
declare(strict_types=1);


namespace App\Application\DTO\Connection;


use DateTime;

class ReadingHistoryDto
{
    public DateTime $period;
    public ?float $value;
    public ?float $consumption;
    public ?string $comment;

    public function __construct(DateTime $period, ?float $value, ?float $consumption, ?string $comment)
    {
        $this->period = $period;
        $this->value = $value;
        $this->consumption = $consumption;
        $this->comment = $comment;
    }


}