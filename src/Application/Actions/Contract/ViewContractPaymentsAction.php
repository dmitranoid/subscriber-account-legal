<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;

use App\Application\Actions\Action;
use App\Application\Service\Contract\ContractInfoService;
use App\Application\Service\Contract\ContractPaymentsService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractPaymentsAction extends Action
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNo);
        $contractPaymentsService = new ContractPaymentsService($this->pdo);
        $docList = $contractPaymentsService->getPaymentsAndInvoicesList($contractNo);
        return $this->view->render(
            $this->response,
            'contract/payments.twig',
            [
                'contract' => $contractInfo,
                'docList' => $docList
            ]
        );
    }
}
