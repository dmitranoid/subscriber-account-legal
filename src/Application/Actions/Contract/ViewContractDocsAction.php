<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Service\Contract\ContractDocsService;
use App\Application\Service\Contract\ContractInfoService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractDocsAction extends Action
{
    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNo);
        $docsService = new ContractDocsService(
            new DocumentFinder('d:/work/legal-docs/')
        );

        $docList = $docsService->getDocumentsByContract(
            $contractNo,
            \DateTime::createFromFormat('dmY', '20190101'),
            \DateTime::createFromFormat('dmY', '20191231')
        );

        return $this->view->render(
            $this->response,
            'contract/docs.twig',
            [
                'contract' => $contractInfo, 
                'docsList' => $docList
            ]
        );
    }

}
