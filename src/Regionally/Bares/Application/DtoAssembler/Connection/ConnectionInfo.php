<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoAssembler\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Application\DTO\Connection\CounterDto;
use DateTimeImmutable;

class ConnectionInfo
{
    public static function transform($data): ConnectionDto
    {
        return new ConnectionDto(
            $data['kp'],
            $data['nab'],
            $data['naim'],
            $data['sr'],
            $data['tarnaim'],
            empty($data['nst']) ? null :
                new CounterDto(
                    $data['tsch'] ?? '-',
                    $data['nst'],
                    $data['ktt'],
                    empty($data['datu']) ? null : new DateTimeImmutable($data['datu']),
                    empty($data['godpov']) ? null : DateTimeImmutable::createFromFormat('Y-m-d', implode('-', [$data['godpov'], intval($data['kvpov'] ?? 1) * 3, '01'])),
                    floatval($data['ppok']),
                    floatval($data['mrasp'] ?? 0),
                )
        );
    }
}