<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;

abstract class ContractAction extends Action
{

    public function __construct(LoggerInterface $logger, ViewInterface $view, Messages $flash)
    {
        parent::__construct($logger, $view, $flash);
    }
}