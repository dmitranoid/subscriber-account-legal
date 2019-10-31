<?php
declare(strict_types=1);


namespace App\Application\Actions\Contract;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ViewContractsListAction extends Action
{
    protected function action(): Response
    {
        $contactsRepository = new SqlContractRepository($this->pdo);
        $contractsList = $contactsRepository->getIndexList();
        return $this->view->render(
            $this->response,
            'misc/contractsIndex.twig',
            ['contractsList' => $contractsList]
        );
    }

}