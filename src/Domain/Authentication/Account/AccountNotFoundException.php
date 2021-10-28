<?php
declare(strict_types=1);

namespace App\Domain\Authentication\Account;

use App\Domain\DomainException\DomainRecordNotFoundException;

class AccountNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The account you requested does not exist.';
}
