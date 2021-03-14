<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoAssembler\Connection;


use App\Application\DTO\Connection\ConnectionDto;
use App\Application\DTO\Connection\CounterDto;

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
            new CounterDto(
                $data['tsch'],
                $data['nst'],
                $data['ktt'],
                empty($data['datu']) ? null : new \DateTime($data['datu']),
            )
        );
    }
}