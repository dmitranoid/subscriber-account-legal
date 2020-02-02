<?php
declare(strict_types=1);

namespace App\Application\View;


class TwigFash
{
    /**
     * @var array
     */
    private $messages;

    /**
     * TwigFash constructor.
     * @param array $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function old($field, $default = '')
    {
        return $this->messages[$field . '.old'][0] ?? $default;
    }

    public function error($field, $glue = '<br>')
    {
        if (isset($this->messages[$field . '.error'])) {
            return implode($glue, $this->messages[$field . '.error'][0]) ?? '';
        } else {
            return '';
        }
    }

}