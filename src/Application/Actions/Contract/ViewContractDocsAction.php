<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Contract\ContractDocsService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Document\DocumentServiceInterface;
use App\Application\View\ViewInterface;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;

class ViewContractDocsAction extends Action
{
    public function __construct(LoggerInterface $logger, ViewInterface $view, PDO $pdo, Messages $flash)
    {
        parent::__construct($logger, $view, $pdo, $flash);
    }


    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        if (empty($contractNo)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }
        $contractInfo = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNo);
        $docsService = AppHelper::container()->get(DocumentServiceInterface::class);

        $docList = $docsService->getDocumentsByContract(
            $contractNo,
            \DateTimeImmutable::createFromFormat('Y-m-d', '2019-01-01'),
            new \DateTimeImmutable('now')
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
