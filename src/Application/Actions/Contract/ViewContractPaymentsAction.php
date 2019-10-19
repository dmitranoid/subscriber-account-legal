<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use App\Infrastructure\Persistence\Invoice\SqlInvoiceRepository;
use App\Infrastructure\Persistence\Payment\SqlPaymentRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractPaymentsAction extends Action
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractRepository = new SqlContractRepository($this->pdo);
        $contractInfo = $contractRepository->getById($contractNo);
        $payments = (new SqlPaymentRepository($this->pdo))->getByContract($contractNo);
        $invoices = (new SqlInvoiceRepository($this->pdo))->getByContract($contractNo);
        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
//        $invoices = [];
        foreach ($invoices as $inv) {
            $paydocs[] = [
                'date' => $inv['data'],
                'period' => $inv['rp'],
                'doctype' => $inv['vids'],
                'docno' => '?',
                'sumInv' => $inv['sum'],
                'sumPay' => ''
            ];
        }

        foreach ($payments as $paym) {
            $paydocs[] = [
                'date' => $paym['data'],
                'period' => $paym['rp'],
                'doctype' => $paym['vids'],
                'docno' => '?',
                'sumInv' =>  '',
                'sumPay' => $paym['sum']
            ];
        }
        $docList = array_reverse($paydocs);

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
