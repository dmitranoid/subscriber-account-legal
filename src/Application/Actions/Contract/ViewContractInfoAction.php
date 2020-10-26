<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Application\Middleware\SessionMiddleware;
use App\Application\Service\Contract\ContractInfoServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractInfoAction extends Action
{
    protected function action(): Response
    {
        $session = $this->request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $contractNo = $session['user']['code_contract'] ?? 0;
        /** @var ContractInfoServiceInterface $service */
        $service = (RegionallyServiceFactory::get('Contract\\ContractInfo'));
        $contractInfo = $service->getContractInfo($contractNo);
        $contractInfo = (new ContractInfoServiceInterface($this->pdo))->getContractInfo($contractNo);
        return $this->view->render($this->response, 'front/contract/info.twig', ['contract' => $contractInfo]);
    }
}