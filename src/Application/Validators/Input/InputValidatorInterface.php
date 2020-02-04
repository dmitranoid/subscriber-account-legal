<?php


namespace App\Application\Validators\Input;


interface InputValidatorInterface
{
    /**
     * ValidatorInterface constructor.
     * @param array|null $rules
     * @param array|null $data
     * @param array|null $fieldsLabels
     */
    public function __construct(array $rules = null, array $data = null, array $fieldsLabels = null);
    public function setRules($rules):void;
    public function setData($data):void;
    public function setFieldsLabels($labels):void;
    public function validate():bool;
    public function getErrors():?array;
}