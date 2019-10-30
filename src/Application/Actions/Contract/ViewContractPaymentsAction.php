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
        $paymentsList = (new SqlPaymentRepository($this->pdo))->getByContract($contractNo);
        $invoicesList = (new SqlInvoiceRepository($this->pdo))->getByContract($contractNo);
        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
//        $invoices = [];
        foreach ($invoicesList as $invoice) {
            $paydocs[] = [
                'date' => $invoice['data'],
                'period' => $invoice['rp'],
                'doctype' => $invoice['vids'],
                'docno' => '?',
                'sumInv' => $invoice['sum'],
                'sumPay' => ''
            ];
        }

        foreach ($paymentsList as $payment) {
            $paydocs[] = [
                'date' => $payment['data'],
                'period' => $payment['rp'],
                'doctype' => $payment['vids'],
                'docno' => '?',
                'sumInv' =>  '',
                'sumPay' => $payment['sum']
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
