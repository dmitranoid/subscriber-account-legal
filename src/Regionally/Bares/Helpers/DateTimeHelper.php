<?php
declare(strict_types=1);

namespace App\Regionally\Bares\Helpers;


use DateTime;
use DateTimeImmutable;

class DateTimeHelper
{
    public static function isDbfDateValid(?string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        if (empty(trim($date))) {
            return false;
        }
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function dateFromQuarter($year, $quarter, $day = 01): ?DateTimeImmutable
    {
        return self::dateFromParts($year, (int)$quarter ?: 1 * 3, $day);
    }

    public static function dateFromParts($year, $month, $day = 01): ?DateTimeImmutable
    {
        $day = $day ?? 1;
        return DateTimeImmutable::createFromFormat('Y-m-d', implode('-', [$year, $month, $day])) ?: null;
    }

}