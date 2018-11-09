<?php

namespace Eme\Core\Util;

/**
 *
 */
class Strings
{
    /**
     *
     */
    public static function camelCaseToUnderscore($input)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
    }
    /**
     *
     */
    public static function underscoreToCamelCase($input)
    {
        $input = trim(strtolower($input), '_');
        return preg_replace_callback('/(?!^)_+([a-z])/', function ($string) {
            return strtoupper($string[1]);
        }, $input);
    }
}
