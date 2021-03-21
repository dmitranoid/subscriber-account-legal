<?php
declare(strict_types=1);


namespace App\Application\Service\Finance;


interface FinanceServiceInterface
{
    public function getDebt($contractNo, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): array;

    public function getPaymentsAndInvoicesList(string $contractNo): array;

}