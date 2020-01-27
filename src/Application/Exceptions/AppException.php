<?php
declare(strict_types=1);

namespace App\Application\Exceptions;


class AppException extends \Exception
{
    protected $message = 'Ошибка работы приложения';
}