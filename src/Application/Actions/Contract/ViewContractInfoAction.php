<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractInfoAction extends ContractAction
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractRepository = new FoxproContractRepository();
        $contractInfo = $contractRepository->getByNomArr($contractNo);
        return $this->view->render($this->response, 'contract/info.twig', ['contract'=>$contractInfo]);
//        return $this->respondWithData($contractInfo);
    }

}