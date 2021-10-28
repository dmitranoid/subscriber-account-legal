<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class ViewContractsListAction extends Action
{
    protected function action(): Response
    {
        $contactsRepository = new SqlContractRepository($this->pdo);
        $contractsList = $contactsRepository->getIndexList();
        return $this->view->render(
            $this->response,
            'front/misc/contractsIndex.twig',
            ['contractsList' => $contractsList]
        );
    }

}