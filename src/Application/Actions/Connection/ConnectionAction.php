<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

abstract class ConnectionAction extends Action
{

    public function __construct(LoggerInterface $logger, Twig $view, \PDO $pdo)
    {
        parent::__construct($logger, $view, $pdo);
    }
}