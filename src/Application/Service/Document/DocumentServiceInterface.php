<?php
declare(strict_types=1);

namespace App\Application\Service\Document;


interface DocumentServiceInterface
{
    public function getDocumentsByContract($contractNo, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): ?array;

}