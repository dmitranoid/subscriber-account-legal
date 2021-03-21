<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Finance;


use App\Application\DTO\Finance\FinanceRecordDto;
use App\Application\Service\Finance\FinanceServiceInterface;
use App\Regionally\Bares\Application\DtoAssembler\Finance\FinanceRecInvoice;
use App\Regionally\Bares\Application\DtoAssembler\Finance\FinanceRecPayment;
use App\Regionally\Bares\Infrastructure\ReadModel\Invoice\InvoiceFinder;
use App\Regionally\Bares\Infrastructure\ReadModel\Payment\PaymentFinder;
use DateTimeImmutable;
use PDO;

final class FinanceService implements FinanceServiceInterface
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDebt($contractNo, DateTimeImmutable $periodStart, DateTimeImmutable $periodEnd): array
    {
        $paymentsSum = (new PaymentFinder($this->pdo))->getSumByContract($contractNo, $periodStart, $periodEnd);
        $invoicesSum = (new InvoiceFinder($this->pdo))->getSumByContract($contractNo, $periodStart, $periodEnd);
        return ['invoicesSum' => $invoicesSum['sumTotal'], 'paymentsSum' => $paymentsSum['sumTotal']];
    }

    /**
     * @param string $contractNo
     * @return FinanceRecordDto[]
     */
    public function getPaymentsAndInvoicesList(string $contractNo): array
    {
        $invoiceDocTypes = ['V' => 'счет', 'R' => 'счет', 'A' => 'акт', '1' => '1 аванс', '2' => '2 аванс', '3' => '3 аванс'];
        $paymentDocTypes = ['V' => 'поступление д/с', 'W' => 'поступление д/с', 'R' => 'поступление д/с', 'Z' => 'взаимозачет'];
        $paymentsList = (new PaymentFinder($this->pdo))->getByContract($contractNo);
        $invoicesList = (new InvoiceFinder($this->pdo))->getByContract($contractNo);
        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
        foreach ($invoicesList as $invoice) {
            $invoice['rp'] = $this->convertRpToDate($invoice['rp']);
            $invoice['doctype'] = $invoiceDocTypes[$invoice['vpt']] ?? '?';
            $docList[] = FinanceRecInvoice::transform($invoice);
        }

        foreach ($paymentsList as $payment) {
            $period = $this->convertRpToDate($payment['rp']);
            $payment['rp'] = $this->convertRpToDate($payment['rp']);
            $payment['doctype'] = $paymentDocTypes[$payment['vids']] ?? '?';
            $docList[] = FinanceRecPayment::transform($payment);
        }
        usort($docList, function (FinanceRecordDto $item1, FinanceRecordDto $item2) {
            return $item1->date <=> $item2->date;
        });

        return array_reverse($docList);
    }

    private function convertRpToDate(string $rp): DateTimeImmutable
    {
        $year = 1900 + (int)substr($rp, 0, 3);
        $month = (int)substr($rp, 3, 2);
        return DateTimeImmutable::createFromFormat('Ymd', sprintf('%04d%02d%02d', $year, $month, 01));
    }

}