<?php

namespace Eme\Core\Http;

use Eme\Core\Util\Strings;
use Eme\Core\Util\Parameter;

/**
 *
 */
class Server extends Parameter
{
    /**
     *
     */
    public function __construct(array $server)
    {
        $server['SERVER_SIGNATURE'] = trim(strip_tags($server['SERVER_SIGNATURE'] ?? ''));
        parent::__construct($server);
    }
    /**
     *
     */
    public function get($key, $default = null)
    {
        if (strpos($key, '_') === false) {
            $key = Strings::camelCaseToUnderscore($key);
        }

        return parent::get(strtoupper($key), $default);
    }
}
