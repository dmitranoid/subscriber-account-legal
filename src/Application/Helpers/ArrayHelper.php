<?php
declare(strict_types=1);

namespace App\Application\Helpers;

/**
 * Возвращает массив $data содержащий только ключи из $keysToKeep
 * @param array $data
 * @param array $keysToKeep
 * @return array
 */

class ArrayHelper
{
    public static function filterArrayKeys(array $data, array $keysToKeep): array
    {
        return !empty($keysToKeep) ? array_intersect_key($data, array_flip($keysToKeep)) : $data;
    }

}