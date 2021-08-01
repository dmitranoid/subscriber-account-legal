<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\User\UserService;
use App\Domain\Authentication\Account\AccountRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUsersListAction extends UserAction
{

    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $account = $auth->getCurrentAccount();
        /** @var AccountRepositoryInterface $userRepository */
        if ($account->isManager()) {
            /** @var UserService $usersService */
            $usersService = AppHelper::container()->get(UserService::class);
            $usersList = $usersService->getUsersList();
        }
        if ($auth->isAdmin()) {

            $usersList = $userRepository->getUsers();
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
