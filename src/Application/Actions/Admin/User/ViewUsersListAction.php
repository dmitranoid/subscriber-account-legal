<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\User\UserServiceInterface;
use App\Infrastructure\ReadModel\PagerSettings;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUsersListAction extends UserAction
{

    protected function action(): Response
    {
        /** @var AuthService $auth */
        $auth = AppHelper::container()->get(AuthService::class);
        $account = $auth->getCurrentAccount();

        parse_str($this->request->getUri()->getQuery(), $params);
        $pager = new PagerSettings($params['page'] ?? 1, 50);

        /** @var UserServiceInterface $usersService */
        $usersService = AppHelper::container()->get(UserServiceInterface::class);

        if ($account->isManager()) {
            $usersList = $usersService->getCustomersList($pager);
        }
        if ($account->isAdmin()) {
            $usersList = $usersService->getManagersList($pager);
        }

        return $this->view->render(
            $this->response,
            'admin/user/index.twig',
            [
                'title' => 'Список пользователей',
                'customersList' => $usersList ?? [],
            ]
        );
    }
}
