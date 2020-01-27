<?php


namespace App\Application\Validator;


interface ValidatorInterface
{

    function setRules($rules):void;
    function setData($data):void;
    function validate():bool;
    function getErrors():?array;
}