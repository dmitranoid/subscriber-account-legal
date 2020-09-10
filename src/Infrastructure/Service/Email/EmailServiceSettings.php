<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Email;


class EmailServiceSettings
{
    public string $host ;
    public int $port = 25;
    public bool $SMTPAuth = true;
    public string $username;
    public string $password;
    public string $from;


    public function __construct(string $host, int $port, bool $SMTPAuth, string $username, string $password, string $from)
    {
        $this->host = $host;
        $this->port = $port;
        $this->SMTPAuth = $SMTPAuth;
        $this->username = $username;
        $this->password = $password;
        $this->from = $from;
    }
}
