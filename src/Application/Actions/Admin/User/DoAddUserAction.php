<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\User;


use App\Application\Service\User\UserService;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class DoAddUserAction extends UserAction
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        // todo надо репозитории вынести в фабрику, чтото типа $repositoryFactory::produce('UserRepositoryInterface)
        $userService = new UserService(new SqlUserRepository($this->pdo));
        if (!$this->request->getAttribute('login')) {
            $userService->add($formData);
        } else {
            $userService->edit($formData);
        }

        return $this->response->withHeader('Location', '/admin/users');
    }
}