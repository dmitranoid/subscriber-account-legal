<?php
declare(strict_types=1);


namespace App\Application\Service\Contract;


use App\Infrastructure\Persistence\Invoice\SqlInvoiceRepository;
use App\Infrastructure\Persistence\Payment\SqlPaymentRepository;

final class ContractPaymentsService
{

    /** @var \PDO */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPaymentsAndInvoicesList($contractNo)
    {
        $paymentsList = (new SqlPaymentRepository($this->pdo))->getByContract($contractNo);
        $invoicesList = (new SqlInvoiceRepository($this->pdo))->getByContract($contractNo);
        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
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

        return $docList;
    }

}