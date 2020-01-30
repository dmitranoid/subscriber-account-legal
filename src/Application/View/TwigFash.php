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
     */
    public function __construct(array $messages)
    {

        $this->messages = $messages;
    }

    public function old($field, $default = '')
    {
        return $this->messages[$field . '.old'][0] ?? $default;
    }

    public function error($field, $glue='<br>')
    {
        return implode($glue, $this->messages[$field . '.error'][0]) ?? '';
    }

}