<?php

namespace Eme\Core\Autoload\Exception;

class InvalidArgumentException extends \Exception
{
    /**
     *
     */
    public function __construct($message, $code = 1)
    {
        parent::__construct($message, $code);
    }
}
