<?php
declare(strict_types=1);

namespace App\Application\View;


class TwigTextHelper
{
    public static function connectionLink(string $abonentNo, string $connectionName): string
    {
        return $abonentNo . '-' . str_replace(['.', '/', '\\'], ['dot', 'slashr', 'slashl'], $connectionName);
    }

    public static function counterCode(string $contractNo, string $abonentNo, string $counterSerialNo): string
    {
        $counterSerialNo = str_replace('.', '_', $counterSerialNo);
        return "cnt-{$contractNo}-{$abonentNo}-{$counterSerialNo}";
    }
}