<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Service\Auth\UserAuthService;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUsersListAction extends UserAction
{

    protected function action(): Response
    {
        $auth = new UserAuthService($this->logger, $this->pdo, $this->request->getAttribute('session',[]));
        $userRepository = new SqlUserRepository($this->pdo);
        if ($auth->isManager()) {
            $customersList = $userRepository->getCustomers();
        }
        if ($auth->isAdmin()) {
            $customersList = $userRepository->getUsers();
        }
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
