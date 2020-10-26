<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoAssembler\Contract;


use App\Application\DTO\Contract\ContractDto;

class ContractInfo
{
    public static function transform($data): ContractDto
    {
        return new ContractDto(
            $data['kp'],
            $data['dop'],
            new \DateTime($data['datd']),
            empty($data['npp2']) ? $data['npp1'] . $data['npp2'] : $data['np1'],
            $data['kodni'],
            implode(' ', [$data['tip_np'], $data['adres2'], $data['tip_ul'], $data['adres3'], $data['tip_kv'], $data['adres4']]),
            $data['fio'],
            $data['nt'],
            $data['rs'] . ' в ' . $data['kb'],
            $data['paybank'] ?? 'наш банк',
            $data['pw']
        );
    }
}