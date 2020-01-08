<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Infrastructure\Persistence\User\SqlUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ListUsersAction extends UserAction
{

    protected function action(): Response
    {
        $userRepository = new SqlUserRepository($this->pdo);
        $customersList = $userRepository->getCustomers();
        return $this->view->render(
            $this->response,
            'admin/user/index.twig',
            [
                'title' => 'Список пользователей',
                'customersList' => $customersList,
            ]
        );
    }
}
