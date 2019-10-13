<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DoLogoutAction extends AuthAction
{

    protected function action(): Response
    {
        if (isset($_SESSION)) {
            unset($_SESSION['contract']);
        }
        return $this->response->withHeader('Location', '/login');
    }
}