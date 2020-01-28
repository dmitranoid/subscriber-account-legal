<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Service\User\CustomerService;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{

    protected function action(): Response
    {
        $userLogin = (string) isset($this->args['login']) ? $this->resolveArg('login') : null;
        $userService = new CustomerService($this->pdo);

        if (empty($userLogin)) {
            $user = $userService->getEmptyUser();
            $title = 'Новый пользователь';
        } else {
            $user = $userService->getCustomerbyLogin($userLogin);
            $title = 'Редактирование пользователя';
        }
        return $this->view->render($this->response, '/admin/user/show.twig', ['user' => $user, 'title' => $title]);
    }
}
