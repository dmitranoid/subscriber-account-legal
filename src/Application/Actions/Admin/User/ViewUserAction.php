<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Helpers\AppHelper;
use App\Application\Service\Auth\AuthService;
use App\Application\Service\User\UserServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{

    protected function action(): Response
    {
        $auth = AppHelper::container()->get(AuthService::class);
        $account = $auth->getCurrentAccount();
        /** @var string|null $userLogin */
        $userLogin = (string)isset($this->args['login']) ? $this->resolveArg('login') : null;
        /** @var UserServiceInterface $usersService */
        $usersService = AppHelper::container()->get(UserServiceInterface::class);

        if (empty($userLogin)) {
//            $user = $userService->getEmptyUser();
            $user = [
                'login' => '',
                'code_group' => '',
                'code_contract' => '',
                'statuses' => '',
            ];
            $title = 'Новый пользователь';
        } else {
            $user = $usersService->getByLoginAndRegion($userLogin, $account->getRegion());
            $title = 'Редактирование пользователя';

        }
        return $this->view->render($this->response, '/admin/user/show.twig', ['user' => $user, 'title' => $title]);
    }
}
