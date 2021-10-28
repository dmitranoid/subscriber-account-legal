<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\User;


use App\Application\Exceptions\AppException;
use App\Application\Service\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{
    protected function action(): Response
    {
        $login = $this->resolveArg('login');
        $userService = new UserService($this->pdo, $this->logger);
        try {
            $userService->deleteByLoginAndRegion($login,);
            $this->flash->addMessage('info', 'пользователь удалён');
        } catch (AppException $e) {
            $this->flash->addMessage('error', 'ошибка удаления пользователя');
        }
        return $this->withRedirect('/admin/users');
    }
}