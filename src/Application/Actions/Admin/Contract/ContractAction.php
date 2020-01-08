<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\Contract;


use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

abstract class ContractAction extends Action
{
    protected function action(): Response
    {

        $data = [];
        return $this->view->render($this->response, '/admin/contract/index.twig', $data);
    }


}