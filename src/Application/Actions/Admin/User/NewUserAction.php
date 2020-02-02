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
                'alphaNum' => ['login'],
                'email' => ['email'],
                'equals' => [['password', 'repassword']],
            ],
            $formData,
            [
                'login' => 'логин',
                'password' => 'пароль',
                'repassword' => 'повтор пароля',
                'code_group' => 'категория',
                'code_contract' => 'договор',
                'email' => 'E-mail',
            ]
        );
        if (false === $inputDataValidator->validate()) {
            $this->setFormOldData($formData);
            $this->setFormErrors($inputDataValidator->getErrors());
            return $this->response->withHeader('Location', '/admin/user');
        }
        // todo надо репозитории вынести в фабрику, чтото типа $repositoryFactory::produce('UserRepositoryInterface)
        $userService = new UserService(new SqlUserRepository($this->pdo));

        if ($this->request->getAttribute('login')) {
            // обновляем
            //$existingUserValidator->validate($formData);
            $userService->edit($formData);
        } else {
            // добавляем
            //$formData = $newUserValidator->validate($formData);
            $userService->add($formData);
        }

        return $this->response->withHeader('Location', '/admin/users');
    }
}