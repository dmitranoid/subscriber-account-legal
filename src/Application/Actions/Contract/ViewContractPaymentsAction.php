<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Invoice\FoxproInvoiceRepository;
use App\Infrastructure\Persistence\Payment\FoxproPaymentRepository;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractPaymentsAction extends ContractAction
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractRepository = new FoxproContractRepository();
        $contractInfo = $contractRepository->getByNomArr($contractNo);
        $payments = (new FoxproPaymentRepository())->getByContract($contractNo);
        $invoices = (new FoxproInvoiceRepository())->getByContract($contractNo);
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
