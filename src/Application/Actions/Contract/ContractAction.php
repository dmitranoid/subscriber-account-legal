<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use App\Domain\Contract\ContractRepository;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

abstract class ContractAction extends Action
{

    /** @var ContractRepository*/
    protected $contractRepository;

    public function __construct(LoggerInterface $logger, Twig $view, ContractRepository $contractRepository)
    {
        parent::__construct($logger, $view);
        $this->contractRepository = $contractRepository;
    }
}