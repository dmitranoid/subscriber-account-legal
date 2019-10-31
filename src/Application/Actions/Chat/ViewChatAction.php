<?php
declare(strict_types=1);


namespace App\Application\Actions\Chat;


use App\Application\Actions\Action;
use App\Application\Service\Contract\ContractInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;


class ViewChatAction extends Action
{

    public function __construct(LoggerInterface $logger, Twig $view, \PDO $pdo)
    {
        parent::__construct($logger, $view, $pdo);
    }

    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNo);

        return $this->view->render($this->response, 'chat/index.twig', ['contract'=>$contractInfo]);
    }

}