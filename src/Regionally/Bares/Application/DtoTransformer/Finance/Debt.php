<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoTransformer\Finance;


use App\Application\DTO\Finance\DebtDto;
use App\Regionally\Bares\Helpers\DateTimeHelper;
use DateTimeImmutable;

class Debt
{
    public static function transform($data): DebtDto
    {
        $date = isset($data['rp']) && DateTimeHelper::isDbfDateValid($data['rp']) ?
            new DateTimeImmutable($data['rp']) :
            new DateTimeImmutable('now');
        return new DebtDto(
            $data['kp'],
            $date,
            (float)$data['dolg01']
        );
    }
}