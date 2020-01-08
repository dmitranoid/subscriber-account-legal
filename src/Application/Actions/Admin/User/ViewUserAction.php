<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use Psr\Http\Message\ResponseInterface as Response;

class ViewUserAction extends UserAction
{

    protected function action(): Response
    {
        $userId = (int) $this->resolveArg('id');
        $userService = new UserService
        $user = $this->userRepository->findUserOfId($userId);
        return $this->respondWithData($user);
    }
}
