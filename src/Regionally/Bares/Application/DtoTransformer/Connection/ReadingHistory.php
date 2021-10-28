<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\DtoTransformer\Connection;


use App\Application\DTO\Connection\ReadingHistoryDto;

class ReadingHistory
{
    public static function transform(array $data): ReadingHistoryDto
    {
//            $data['rp_part_1'] = substr($item['rppok'], 0, 1);
//            $data['rp_part_m'] = substr($data['rppok'], 3, 2);
//            $data['rp_part_y'] = substr($data['rppok'], 1, 2);
        $dateStr = '01' . substr($data['rppok'], 3, 2) . '20' . substr($data['rppok'], 1, 2);
        return new ReadingHistoryDto(
            \DateTime::createFromFormat("dmY", $dateStr),
            (float)$data['tpok'],
            (float)$data['tpok'] - (float)$data['ppok'],
            $data['comment'] ?? ''
        );
    }
}