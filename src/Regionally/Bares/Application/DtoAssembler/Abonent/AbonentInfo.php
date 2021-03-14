<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoAssembler\Abonent;


use App\Application\DTO\Abonent\AbonentInfoDto;

class AbonentInfo
{
    public static function transform($data): AbonentInfoDto
    {
        $address = $data['tip_np'] . $data['name_np'] . ', '
            . $data['tip_ul'] . $data['name_ul'] . ', '
            . (!empty($data['dom']) ? 'д. ' . $data['dom'] . ' ' . $data['korpus'] : '');
        return new AbonentInfoDto(
            $data['kp'],
            $data['nab'],
            $data['naim'],
            $address
        );
    }
}