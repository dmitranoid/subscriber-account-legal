<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractDocsService;
use App\Application\Service\Contract\ContractInfoService;
use App\Application\View\TwigFash;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use DateTime;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class ViewContractDocsAction extends Action
{
    public function __construct(LoggerInterface $logger, Twig $view, PDO $pdo, Messages $flash)
    {
        parent::__construct($logger, $view, $pdo, $flash);
    }


    protected function action(): Response
    {
        $session = $this->request->getAttribute('session', []);
        $auth = new UserAuthService($this->logger, $this->pdo, $session);
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        // TODO брать путь к документам из настроек
        $documentsStorageRoot = 'F:/lkprom-docs/';  // под windows буква диска с большой буквы !!!
        $docsService = new ContractDocsService(
            new DocumentFinder($documentsStorageRoot),
            $documentsStorageRoot
        );

        $docList = $docsService->getDocumentsByContract(
            $contractNom,
            DateTime::createFromFormat('dmY', '20190101'),
            DateTime::createFromFormat('dmY', '20201231')
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
