<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use App\Domain\Contract\ContractRepository;
use PDO;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

abstract class ContractAction extends Action
{

    protected ContractRepository $contractRepository;

    public function __construct(LoggerInterface $logger, ViewInterface $view, ContractRepository $contractRepository, PDO $pdo, Messages $flash)
    {
        parent::__construct($logger, $view, $pdo,$flash);
        $this->contractRepository = $contractRepository;
    }
}