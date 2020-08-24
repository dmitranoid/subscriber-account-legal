<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;

use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Service\Auth\UserAuthService;
use App\Application\Service\Contract\ContractInfoService;
use App\Application\Service\Contract\ContractPaymentsService;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Service\Session\Session;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractPaymentsAction extends Action
{
    protected function action(): Response
    {
        $auth = new UserAuthService($this->logger, $this->pdo, new Session());
        $contractNom = $auth->getUser()['code_contract'] ?? null;
        if (empty($contractNom)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }

        $contractInfo = (new ContractInfoService($this->pdo))->getContractInfo($contractNom);
        $contractPaymentsService = new ContractPaymentsService($this->pdo);
        $date = new DateTime('now');
        $debtInfo = [
            'date' => new DateTime('first day of this month'),
            'sum'=>$contractInfo['dolg01'],
        ];
        // $contractPaymentsService->getDebt($contractNom, \DateTime::createFromFormat('Ymd', '20190101'), \DateTime::createFromFormat('Ymd', '20191231'));
        $docList = $contractPaymentsService->getPaymentsAndInvoicesList($contractNom);
        return $this->view->render(
            $this->response,
            'front/contract/payments.twig',
            [
                'contract' => $contractInfo,
                'debtInfo'=>$debtInfo,
                'docList' => $docList
            ]
        );
    }
}
