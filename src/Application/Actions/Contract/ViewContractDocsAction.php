<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Service\Contract\ContractDocsService;
use App\Application\Service\Contract\ContractInfoService;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class ViewContractDocsAction extends Action
{
    public function __construct(LoggerInterface $logger, Twig $view, PDO $pdo)
    {
        parent::__construct($logger, $view, $pdo);
    }


    protected function action(): Response
    {
        $contractNo = $_SESSION['contract'];
        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNo);
        // TODO брать путь к документам из настроек
        $documentsStorageRoot = 'D:/work/legal-docs/';
        $docsService = new ContractDocsService(
            new DocumentFinder($documentsStorageRoot),
            $documentsStorageRoot
        );

        $docList = $docsService->getDocumentsByContract(
            $contractNo,
            \DateTime::createFromFormat('dmY', '20190101'),
            \DateTime::createFromFormat('dmY', '20201231')
        );

        return $this->view->render(
            $this->response,
            'front/contract/docs.twig',
            [
                'contract' => $contractInfo, 
                'docsList' => $docList
            ]
        );
    }

}
