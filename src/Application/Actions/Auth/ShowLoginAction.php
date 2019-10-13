<?php
declare(strict_types=1);


namespace App\Application\Actions\Auth;


use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;


class ShowLoginAction extends AuthAction
{

    protected function action(): Response
    {
        if (isset($_SESSION['contract'])) {
            return $this->response->withHeader('Location', '/contract/info');
        }
        return $this->view->render($this->response, 'auth/login.twig');
    }

}