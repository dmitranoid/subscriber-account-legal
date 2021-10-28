<?php
declare(strict_types=1);

namespace App\Application\View;


class TwigFash
{

    private array $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function old(string $field, string $default = ''): string
    {
        return $this->messages[$field . '.old'][0] ?? $default;
    }

    public function error($field, $glue = '<br>'):string
    {
        $fieldIndex = $field . '.error';
        if (isset($this->messages[$fieldIndex])) {
            // для valitron validator
            if(isset($this->messages[$fieldIndex][0]) && is_array($this->messages[$fieldIndex][0])) {
                return implode($glue, $this->messages[$fieldIndex][0]) ?? '';
            }
            return implode($glue, $this->messages[$fieldIndex]);
        }
        return '';
    }

}