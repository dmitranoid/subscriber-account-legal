<?php
declare(strict_types=1);


namespace App\Application\Validator;


use Valitron\Validator;

class ValitronValidator implements ValidatorInterface
{
    /** @var Validator  */
    private $validator;

    public function __construct($rules = null, $data = null)
    {
        Validator::lang('ru');
        $this->validator = new Validator($data);
        $this->validator->rules($rules);

    }

    function setRules($rules): void
    {
        $this->validator->rules($rules);
    }

    function setData($data): void
    {
        $this->validator = $this->validator->withData($data);
    }

    function validate(): bool
    {
        return $this->validator->validate();
    }

    function getErrors(): ?array
    {
        return $this->validator->errors();
    }

}