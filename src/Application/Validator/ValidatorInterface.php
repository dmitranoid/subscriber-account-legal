<?php


namespace App\Application\Validator;


interface ValidatorInterface
{
    /**
     * ValidatorInterface constructor.
     * @param array|null $rules
     * @param array|null $data
     * @param array|null $fieldsLabels
     */
    public function __construct(array $rules = null, array $data = null, array $fieldsLabels = null);
    function setRules($rules):void;
    function setData($data):void;
    function setFieldsLabels($labels):void;
    function validate():bool;
    function getErrors():?array;
}