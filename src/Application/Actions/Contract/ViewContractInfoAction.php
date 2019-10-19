<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractInfoAction extends Action
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractRepository = new SqlContractRepository($this->pdo);
        $contractInfo = $contractRepository->getById($contractNo);
        return $this->view->render($this->response, 'contract/info.twig', ['contract'=>$contractInfo]);
//        return $this->respondWithData($contractInfo);
    }

}