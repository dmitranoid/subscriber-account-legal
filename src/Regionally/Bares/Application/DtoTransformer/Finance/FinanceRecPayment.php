<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoTransformer\Finance;


use App\Application\DTO\Finance\FinanceRecordDto;
use DateTimeImmutable;

class FinanceRecPayment
{
    public static function transform($data): FinanceRecordDto
    {
        return new FinanceRecordDto(
            $data['kp'],
            DateTimeImmutable::createFromFormat(DATE_RFC2822, $data['data']),
            $data['rp'],
            $data['doctype'],
            '',
            0,
            (float)$data['sum'],
        );
    }
}