<?php
declare(strict_types=1);


namespace App\Application\Actions\Admin\User;


use App\Application\Exceptions\AppException;
use App\Application\Helpers\ArrayHelper;
use App\Application\Service\User\UserService;
use App\Application\Validators\Input\ValitronInputValidator;
use Psr\Http\Message\ResponseInterface as Response;

class EditUserAction extends UserAction
{

    /**
     * @inheritDoc
     */
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
            'phones'
        ];
        $formLabels = [
            'login' => 'логин',
            'password' => 'пароль',
            'repassword' => 'повтор пароля',
            'code_group' => 'категория',
            'code_contract' => 'договор',
            'email' => 'E-mail',
            'phones' => 'номер телефона'
        ];
        $formData = ArrayHelper::filterArrayKeys($formData, $allowedFields);
        if (in_array($formData['code_group'], ['admin', 'manager'])) {
            // для администратора и менеджера договор не нужен
            $inputDataValidator = new ValitronInputValidator(
                [
                    'required' => ['login', 'code_group', 'email'],
                    //'alphaNum' => ['login'],
                    'email' => ['email'],
                    'equals' => [['password', 'repassword']],
                ],
                $formData,
                $formLabels
            );

        } else {
            $inputDataValidator = new ValitronInputValidator(
                [
                    'required' => ['login', 'code_group', 'code_contract', 'email'],
                    //'alphaNum' => ['login'],
                    'email' => ['email'],
                    'equals' => [['password', 'repassword']],
                ],
                $formData,
                $formLabels
            );
        }
        if (false === $inputDataValidator->validate()) {
            $this->setFormOldData($formData);
            $this->setFormErrors($inputDataValidator->getErrors());
            return $this->withRedirect('/admin/user' . $formData['login']);
        }
        // todo надо репозитории вынести в фабрику, чтото типа $repositoryFactory::produce('UserRepositoryInterface)
        $userService = new UserService($this->pdo, $this->logger);

        // обновляем пользователя
        try {
            $userService->update($formData);
        } catch (AppException $e) {
            //todo возможно вынести в toast ошибку без привязки к полю
            $this->setFormErrors(['code_contract' => $e->getMessage()]);
            $this->setFormOldData($formData);
            return $this->withRedirect('/admin/user');
        }
        return $this->withRedirect('/admin/users');
    }
}