<?php
declare(strict_types=1);


namespace App\Application\Service\Finance;


use App\Application\DTO\Finance\DebtDto;

interface FinanceServiceInterface
{
    public function getDebt(string $contractNo, \DateTimeImmutable $date): DebtDto;

    public function getPaymentsAndInvoicesList(string $contractNo): array;

}