<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Document;


use App\Application\Service\Document\DocumentServiceInterface;
use App\Domain\Authentication\Account\Account;
use DateTimeImmutable;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CachedDocumentService implements DocumentServiceInterface
{

    private DocumentServiceInterface $cachedService;

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache, DocumentServiceInterface $cachedService)
    {
        $this->cachedService = $cachedService;
        $this->cache = $cache;
    }

    public function getDocumentsByContract($contractNo, DateTimeImmutable $periodStart, DateTimeImmutable $periodEnd): ?array
    {
        try {
            $data = $this->cache->get($contractNo);
            return $data;
        } catch (InvalidArgumentException $e) {
            //TODO log cache error
        }
        // not cached data
        $data = $this->cachedService->getDocumentsByContract($contractNo, $periodStart, $periodEnd);
        $this->cache->set($contractNo, $data);
        return $data;
    }


    public function isAccessToFileAllowedForAccount(Account $account, $filename): bool
    {
        return $this->cachedService->isAccessToFileAllowedForAccount($account, $filename);
    }

    public
    function getDocumentContent(string $filename): string
    {
        return $this->cachedService->getDocumentContent($filename);
    }
}