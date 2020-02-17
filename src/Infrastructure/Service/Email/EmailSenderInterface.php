<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Email;


interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $body):void;
}