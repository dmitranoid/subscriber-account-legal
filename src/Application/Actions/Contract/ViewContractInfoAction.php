<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Helpers\AppHelper;
use App\Application\Middleware\SessionMiddleware;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractInfoAction extends Action
{
    protected function action(): Response
    {
        $session = new Session();
        $accountRepository = AppHelper::container()->get(AccountRepositoryInterface::class);
        $authService = new AuthService($this->logger, $accountRepository, $session);
        /** @var ContractInfoServiceInterface $service */
        $service = AppHelper::container()->get(ContractInfoServiceInterface::class);
        $contractInfo = $service->getContractInfo($authService->getCurrentAccount()->getContractNo());
        return $this->view->render($this->response, 'front/contract/info.twig', ['contract' => $contractInfo]);
    }
}