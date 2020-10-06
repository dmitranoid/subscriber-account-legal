<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Session;


interface SessionInterface
{
    public function open();
    public function destroy();
    public function get(string $key);
    public function set(string $key, $value);
}