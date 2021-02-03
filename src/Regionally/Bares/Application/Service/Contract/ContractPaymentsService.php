<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Contract;


use App\Infrastructure\Persistence\Invoice\SqlInvoiceRepository;
use App\Infrastructure\Persistence\Payment\SqlPaymentRepository;
use DateTime;
use DateTimeImmutable;
use PDO;

final class ContractPaymentsService
{

    /** @var PDO */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDebt($contractNo, DateTime $periodStart, DateTime $periodEnd): array
    {
        $paymentsSum = (new SqlPaymentRepository($this->pdo))->getSumByContract($contractNo, $periodStart, $periodEnd);
        $invoicesSum = (new SqlInvoiceRepository($this->pdo))->getSumByContract($contractNo, $periodStart, $periodEnd);
        return ['invoicesSum' => $invoicesSum, 'paymentsSum' => $paymentsSum];
    }

    public function getPaymentsAndInvoicesList($contractNo)
    {
        $invoiceDocTypes = ['V' => 'счет', 'R' => 'счет', 'A' => 'акт', '1' => '1 аванс', '2' => '2 аванс', '3' => '3 аванс'];
        $paymentDocTypes = ['V' => 'поступление д/с', 'W' => 'поступление д/с', 'R' => 'поступление д/с', 'Z' => 'взаимозачет'];
        $paymentsList = (new SqlPaymentRepository($this->pdo))->getByContract($contractNo);
        $invoicesList = (new SqlInvoiceRepository($this->pdo))->getByContract($contractNo);
        // date, period, doctype, docno, sumInv, sumPay
        $docList = [];
        foreach ($invoicesList as $invoice) {
            $period = $this->convertRpToDate($invoice['rp']);
            $paydocs[] = [
                'date' => DateTime::createFromFormat(DATE_RFC2822, $invoice['data']),
                'period_y' => $period->format('Y'),
                'period_m' => $period->format('m'),
                'doctype' => $invoiceDocTypes[$invoice['vpt']] ?? '?',
                'docno' => '', //$invoice['akt_nomer'],
                'sumInv' => $invoice['sumTotal'],
                'sumPay' => ''
            ];
        }

        foreach ($paymentsList as $payment) {
            $period = $this->convertRpToDate($payment['rp']);
            $paydocs[] = [
                'date' => DateTime::createFromFormat(DATE_RFC2822, $payment['data']),
                'period_y' => $period->format('Y'),
                'period_m' => $period->format('m'),
                'doctype' => $paymentDocTypes[$payment['vids']] ?? '?',
                'docno' => '',
                'sumInv' => '',
                'sumPay' => $payment['sum']
            ];
        }
        usort($paydocs, function ($item1, $item2) {
            return ($item1['date'] ?? 0) < ($item2['date'] ?? 0) ? -1 : 1;
        });

        return array_reverse($paydocs);
    }

    private function convertRpToDate(string $rp): DateTimeImmutable
    {
        $year = 1900 + (int)substr($rp, 0, 3);
        $month = (int)substr($rp, 3, 2);
        return DateTimeImmutable::createFromFormat('Ymd', sprintf('%04d%02d%02d', $year, $month, 01));
    }

}