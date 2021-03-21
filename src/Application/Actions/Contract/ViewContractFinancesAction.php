<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;

use App\Application\Actions\Action;
use App\Application\Exceptions\AppException;
use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Application\Service\Contract\ContractPaymentsService;
use App\Application\Service\Finance\FinanceServiceInterface;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractFinancesAction extends Action
{
    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $contractNo = $auth->getCurrentAccount()->getContractNo();

        if (empty($contractNo)) {
            $this->logger->error('не найден договор');
            throw new AppException('не найден договор');
        }

        /** @var ContractInfoServiceInterface $contractInfo */
        $contractInfo = AppHelper::container()->get(ContractInfoServiceInterface::class)->getContractInfo($contractNo);
        /** @var FinanceServiceInterface $financeService */
        $financeService = AppHelper::container()->get(FinanceServiceInterface::class);

        $date = new DateTime('now');
        $debt = $financeService->getDebt(
            $contractNo,
            \DateTimeImmutable::createFromFormat('Y-m-d', '2020-01-01'),
            \DateTimeImmutable::createFromMutable($date)
        );
        //todo переделать в отдельный  сервис
        $debtInfo = [
            'date' => new DateTime('first day of this month'),
            'sum' => (float)$debt['invoicesSum'] - (float)$debt['paymentsSum'], // todo было $contractInfo['dolg01'],
        ];
        // $financeService->getDebt($contractNo, \DateTime::createFromFormat('Ymd', '20190101'), \DateTime::createFromFormat('Ymd', '20191231'));
        $docList = $financeService->getPaymentsAndInvoicesList($contractNo);
        return $this->view->render(
            $this->response,
            'front/contract/payments.twig',
            [
                'contract' => $contractInfo,
                'debtInfo' => $debtInfo,
                'docList' => $docList
            ]
        );
    }
}
