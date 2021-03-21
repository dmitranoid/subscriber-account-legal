<?php
declare(strict_types=1);

namespace App\Application\Service\Document;


use App\Domain\Authentication\Account\Account;

interface DocumentServiceInterface
{
    public function getDocumentsByContract($contractNo, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): ?array;

    public function isAccessToFileAllowedForAccount(Account $account, $filename): bool;

    public function getDocumentContent(string $filename): string;
}