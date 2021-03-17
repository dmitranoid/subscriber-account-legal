<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractDocsService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Application\View\ViewInterface;
use App\Infrastructure\Service\DocumentFinder\DocumentFinder;
use App\Infrastructure\Service\Session\Session;
use DateTime;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use App\Application\Helpers\AppHelper;

class ViewContractDocsAction extends Action
{
    public function __construct(LoggerInterface $logger, ViewInterface $view, PDO $pdo, Messages $flash)
    {
        parent::__construct($logger, $view, $pdo, $flash);
    }


    protected function action(): Response
    {
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = ($auth->getUser())->code_contract ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $contractInfo = (AppHelper::container()->get(ContractInfoServiceInterface::class))->getContractInfo($contractNom);

        $docsService = AppHelper::container()->get(DocumentServiceInterface::class)->getDocumentsByContract;

        $docList = $docsService->getDocumentsByContract(
            $contractNom,
            DateTime::createFromFormat('Ymd', '20190101'),
            new DateTime('now')
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
