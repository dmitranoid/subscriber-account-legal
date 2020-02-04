<?php
declare(strict_types=1);


namespace App\Application\Validators;


interface ValidatorInterface
{
    public function validate():bool;
}