<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Domain\Contract\ContractNotFoundException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Infrastructure\Persistence\Contract\FoxproContractRepository;
use App\Infrastructure\Persistence\Contract\SqlContractRepository;
use Psr\Http\Message\ResponseInterface as Response;

class DoLoginAction extends AuthAction
{
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $contractNom = $data['nom'] ?? null;
        $password = $data['password'] ?? null;
        if((empty($contractNom)||empty($password)) || ($contractNom != $password)){
            return $this->response->withHeader('Location', '/login');
        }

        $contract = (new SqlContractRepository($this->pdo))->getById($contractNom);
        if (empty($contract)) {
            return $this->response->withHeader('Location', '/login');
        }
        $_SESSION['contract'] = $contractNom;

        return $this->response->withHeader('Location', '/contract');
    }

}