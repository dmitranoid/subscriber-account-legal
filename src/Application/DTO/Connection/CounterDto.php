<?php
declare(strict_types=1);

namespace App\Application\DTO\Connection;


use DateTime;

class CounterDto
{
    public string $model;
    public string $serialNom;
    public string $coeffTr;
    public ?DateTime $installDate;

    public function __construct(string $model, string $serialNom, string $coeffTr, ?DateTime $installDate)
    {
        $this->model = $model;
        $this->serialNom = $serialNom;
        $this->coeffTr = $coeffTr;
        $this->installDate = $installDate;
    }

}