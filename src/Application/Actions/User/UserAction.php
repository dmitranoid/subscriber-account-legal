<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;

abstract class UserAction extends Action
{

    protected AccountRepositoryInterface $accountRepository;

    public function __construct(
        LoggerInterface $logger,
        ViewInterface $view,
        Messages $flash,
        AccountRepositoryInterface $accountRepository
    )
    {
        parent::__construct($logger, $view, $flash);
    }
}
