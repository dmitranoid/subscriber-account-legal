<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\User;


use App\Application\Helpers\ArrayHelpers;
use App\Application\Service\User\UserService;
use App\Application\Validator\ValitronValidator;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

class NewUserAction extends UserAction
{
    protected function action(): Response
    {
        $formData = $this->getFormData();
        $allowedFields = [
            'login',
            'password',
            'repassword',
            'code_group',
            'code_contract',
            'email',
        ];
        $formData = ArrayHelpers::filterArrayKeys($formData, $allowedFields);
        $inputDataValidator = new ValitronValidator(
            [
                'required' => ['login', 'code_group', 'code_contract', 'email'],
                'alpha' => ['login'],
                'email' => ['email'],
                'equals' => [['password', 'repassword']],
            ],
            $formData
        );
        if (false === $inputDataValidator->validate()) {
            $this->setFormOldData($formData);
            $this->setFormErrors($inputDataValidator->getErrors());
            return $this->response->withHeader('Location', '/admin/user');
        }
        // todo надо репозитории вынести в фабрику, чтото типа $repositoryFactory::produce('UserRepositoryInterface)
        $userService = new UserService(new SqlUserRepository($this->pdo));

        if (!$this->request->getAttribute('login')) {
            $formData = $newUserValidator->validate($formData);
            // обновляем
            $userService->add($formData);
        } else {
            // добавляем
            $existingUserValidator->validate($formData);
            $userService->edit($formData);
        }

        return $this->response->withHeader('Location', '/admin/users');
    }
}