<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Service\Auth\AuthService;
use App\Infrastructure\Persistence\Account\SqlAccountRepository;
use App\Infrastructure\Service\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUsersListAction extends UserAction
{

    protected function action(): Response
    {
        $auth = new AuthService($this->logger, $this->pdo, new Session());
        $userRepository = new SqlAccountRepository($this->pdo);
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
                'customersList' => $customersList ?? [],
            ]
        );
    }
}
