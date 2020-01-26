<?php
declare(strict_types=1);


namespace App\Application\Service\User;


use PDO;

class UserService
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getUsersList()
    {
        
    }

    public function save()
    {
        
    }

    public function update()
    {
        
    }

    public function delete()
    {
        
    }
}