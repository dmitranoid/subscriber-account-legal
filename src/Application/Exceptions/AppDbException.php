<?php
declare(strict_types=1);

namespace App\Application\Exceptions;


class AppDbException extends AppException
{
    protected $message = 'Ошибка при работе с БД';
}