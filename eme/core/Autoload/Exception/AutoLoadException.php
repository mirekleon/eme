<?php

namespace Eme\Core\Autoload\Exception;

class AutoLoadException extends \RuntimeException
{
    /**
     *
     */
    public function __construct($message, $code = 1)
    {
        parent::__construct($message, $code);
    }
}
