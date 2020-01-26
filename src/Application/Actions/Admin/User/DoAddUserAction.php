<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\User;


use Psr\Http\Message\ResponseInterface as Response;

class DoAddUserAction extends UserAction
{
    protected function action(): Response
    {
        $formData = $this->getFormData();

        return $this->response->withHeader('Location', '/admin/users');
    }

}