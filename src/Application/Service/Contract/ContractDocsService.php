<?php
declare(strict_types=1);


namespace App\Application\Service\Contract;


use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use DateTime;

final class ContractDocsService
{
    /** @var DocumentFinder */
    private $documentFinderService;
    /** @var string */
    private $docStorageRoot;

    public function __construct(DocumentFinder $documentFinderService, string $docStorageRoot)
    {
        $this->documentFinderService = $documentFinderService;
        $this->docStorageRoot = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $docStorageRoot);
    }

    public function getDocumentsByContract($contractNo, DateTime $periodStart, DateTime $periodEnd): ?array
    {
        $docFilesList = $this->documentFinderService->getDocsByContract($contractNo, $periodStart, $periodEnd);
        $docList = [];
        foreach ($docFilesList as $docFile) {

            $periodData = explode('-', $docFile['datePath']); // 01-2019
            $docName = strpos($docFile['filename'], 'ндс') ? 'сч/ф Энергия' : 'сч/ф НДС';
            $docList[] = [
                'date' => $docFile['modifyDate'],
                'period' => $periodData[0] . '.' . $periodData[1],
                'docName' => $docName,
                'fullFilename' => str_replace($this->docStorageRoot, '', $docFile['fullPath']),  // относительный к $docStorageRoot
            ];
        }

        return array_reverse($docList);
    }

}