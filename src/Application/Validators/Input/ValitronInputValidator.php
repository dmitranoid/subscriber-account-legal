<?php
declare(strict_types=1);


namespace App\Application\Validators\Input;


use App\Application\Validators\Input\InputValidatorInterface;
use App\Application\Validators\ValidatorInterface;
use Valitron\Validator;

class ValitronInputValidator implements InputValidatorInterface, ValidatorInterface
{
    /** @var Validator */
    private $validator;
    /**
     * @var array
     */
    private $fieldsLables;

    /**
     * ValitronValidator constructor.
     * @param array $rules
     * @param array $data
     * @param array $fieldsLabels
     */
    public function __construct(array $rules = null, array $data = null, array $fieldsLabels = null)
    {
        Validator::lang('ru');
        $this->validator = new Validator($data);
        $this->validator->rules($rules);
        $this->validator->labels($fieldsLabels);
    }

    function setRules($rules): void
    {
        $this->validator->rules($rules);
    }

    function setData($data): void
    {
        $this->validator = $this->validator->withData($data);
    }

    function setFieldsLabels($labels): void
    {
        $this->validator->labels($labels);
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