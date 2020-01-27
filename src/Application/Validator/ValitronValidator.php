<?php
declare(strict_types=1);


namespace App\Application\Validator;


class ValitronValidator implements ValidatorInterface
{
    private $rules;

    public function __construct()
    {
    }

    function setRules($rules): void
    {
        // TODO: Implement setRules() method.
    }

    function setData($data): void
    {
        // TODO: Implement setData() method.
    }

    function validate(): bool
    {
        // TODO: Implement validate() method.
    }

    function getErrors(): ?array
    {
        // TODO: Implement getErrors() method.
    }

}