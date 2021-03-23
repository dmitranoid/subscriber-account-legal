<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoTransformer\Finance;


use App\Application\DTO\Finance\FinanceRecordDto;
use DateTimeImmutable;

class FinanceRecInvoice
{
    public static function transform($data): FinanceRecordDto
    {
        return new FinanceRecordDto(
            $data['kp'],
            DateTimeImmutable::createFromFormat(DATE_RFC2822, $data['data']),
            $data['rp'],
            $data['doctype'],
            '',
            (float)$data['sumTotal'],
            0
        );
    }
}