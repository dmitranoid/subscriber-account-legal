<?php
declare(strict_types=1);


namespace App\Application\Service\Contract;


use App\Infrastructure\Service\DocumentFinder\DocumentFinder;

final class ContractDocsService
{
    private $documentFinderService;

    public function __construct(DocumentFinder $documentFinderService)
    {
        $this->documentFinderService = $documentFinderService;
    }

    public function getDocumentsByContract($contractNo, \DateTime $periodStart, \DateTime $periodEnd):?array
    {
        $docFilesList = $this->documentFinderService->getDocsByContract($contractNo, $periodStart, $periodEnd);
        foreach ($docFilesList as $docFile) {
            $periodData = explode('-', $docFile['datePath']); // 01-2019
            $docName = strpos($docFile['filename'], 'ндс') ? 'сч/ф Энергия' : 'сч/ф НДС';
                $docList[] = [
                'date' => $docFile['modifyDate'],
                'period' => $periodData[0] . '.' . $periodData[1],
                'docName' => $docName,
                'fullFilename' => $docFile['fullPath'],
            ];
        }

        return array_reverse($docList);
    }

}