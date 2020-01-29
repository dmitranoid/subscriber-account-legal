<?php


namespace App\Application\Validator;


interface ValidatorInterface
{
    public function __construct($rules = null, $data = null);
    function setRules($rules):void;
    function setData($data):void;
    function validate():bool;
    function getErrors():?array;
}