<?php
declare(strict_types=1);


namespace App\Regionally\Bares\Application\Service\Document;


use App\Application\Exceptions\AppException;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Domain\Authentication\Account\Account;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;

final class DocumentService implements DocumentServiceInterface
{
    private DocumentFinder $documentFinderService;
    private string $docStorageRoot;

    public function __construct(DocumentFinder $documentFinderService, string $docStorageRoot)
    {
        $this->documentFinderService = $documentFinderService;
        $this->docStorageRoot = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $docStorageRoot);
    }

    public function getDocumentsByContract($contractNo, \DateTimeImmutable $periodStart, \DateTimeImmutable $periodEnd): ?array
    {
        $docFilesList = $this->documentFinderService->getDocsByContract($contractNo, $periodStart, $periodEnd);
        $docList = [];
        foreach ($docFilesList as $docFile) {

            $periodData = explode('-', $docFile['datePath']); // пример 01-2019
            $docName = strpos($docFile['filename'], 'ндс') ? 'сч/ф Энергия' : 'приложение к сч/ф';
            $docList[] = [
                'date' => $docFile['modifyDate'],
                'period' => $periodData[0] . '.' . $periodData[1],
                'docName' => $docName,
                'fullFilename' => str_replace($this->docStorageRoot, '', $docFile['fullPath']),  // относительный к $docStorageRoot
            ];
        }
        return array_reverse($docList);
    }

    public function isAccessToFileAllowedForAccount(Account $account, $filename): bool
    {
        // $filename = D:\work\legal-docs\2019\09-2019\802_счет.888 ; $contractNo= 802
        // $filename = D:\work\legal-docs\2019\09-2019\802_ндс.888 ; $contractNo= 802
        $pattern = '%\d{4}.\d{2}-\d{4}.' . $account->getContractNo() . '_(счет|ндс).\d{3}$%iu';
        return (bool)preg_match($pattern, $filename);
    }

    /**
     * @param $filename
     * @return string
     * @throws AppException
     */
    public function getDocumentContent(string $filename): string
    {
        $filenameFull = realpath($this->docStorageRoot . $filename);
        if (!($this->isFilenameValid($filenameFull) && file_exists($filenameFull))) {
            throw new AppException('файл документа не найден');
        }
        return iconv('CP1251', 'UTF-8', file_get_contents($filenameFull));
    }

    private function isFilenameValid($filename): bool
    {
        // $filename = D:\work\legal-docs\2019\09-2019\802_счет.888 ; $contractNo= 802
        // $filename = D:\work\legal-docs\2019\09-2019\802_ндс.888 ; $contractNo= 802
        $pattern = '%\d{4}.\d{2}-\d{4}.\d{1,4}_(счет|ндс).\d{3}$%iu';
        return (bool)preg_match($pattern, $filename);
    }


}