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
        $debtInfo = $contractPaymentsService->getDebt($contractNo, \DateTime::createFromFormat('Ymd', '20190101'), \DateTime::createFromFormat('Ymd', '20191231'));
        $docList = $contractPaymentsService->getPaymentsAndInvoicesList($contractNo);
        return $this->view->render(
            $this->response,
            'front/contract/payments.twig',
            [
                'contract' => $contractInfo,
                'debtInfo'=>$debtInfo,
                'docList' => $docList
            ]
        );
    }
}
