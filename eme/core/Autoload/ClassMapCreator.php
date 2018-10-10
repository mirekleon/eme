<?php

/**
 * This file is part of Eme.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eme\Core\Autoload;

use Eme\Core\Autoload\ClassMapGenarator;

/**
 *
 */
class ClassMapCreator
{
    /**
     *
     */
    public static function create($file, array $map = [])
    {
        $normalized = self::normalize($map);
        self::dump($file, $normalized);
    }
    /**
     *
     */
    public function normalize(array $maps)
    {
        $normalized = "<?php" . PHP_EOL . PHP_EOL;
        $normalized .= self::getHeader() . PHP_EOL . "return [" . PHP_EOL;
        $key = SINGLE_CLASS_IDENTIFIER;
        $single = $maps[$key] ?? [];
        unset($maps[$key]);

        foreach ($maps as $prefix => $namespaces) {
            $normalized .= self::makeTab(4) . "'$prefix' => [" . PHP_EOL;
            foreach ($namespaces as $namespace => $path) {
                $path = preg_replace('/\\\\/', '/', $path);
                $normalized .= self::makeTab(4) . self::appendSingleNamespace($namespace, $path);
            }
            $normalized .= self::makeTab(4) . "]," . PHP_EOL;
        }
        if ($single) {
            $max = self::getMax($single);
            $normalized .= self::makeTab(4) . "'$key' => [" . PHP_EOL;
            foreach ($single as $class => $path) {
                $normalized .= self::appendSingleClass($class, $path, $max);
            }
            $normalized .= self::makeTab(4) . "]," . PHP_EOL;
        }

        return $normalized . "];" . PHP_EOL;
    }
    /**
     *
     */
    public function appendSingleNamespace($namespace, $path)
    {
        $namespace = preg_replace('/\\\\/', "\\\\\\", $namespace);
        return sprintf(
            '%s\'%s\' => \'%s\',%s',
            self::makeTab(4),
            $namespace,
            $path,
            PHP_EOL
        );
    }
    /**
     *
     */
    public function appendSingleClass($className, $path, $max)
    {
        $pad = $max - strlen($className);
        return sprintf(
            '%s%s::class %s=> \'%s\',%s',
            self::makeTab(8),
            $className,
            self::makeTab($pad),
            $path,
            PHP_EOL
        );
    }
    /**
     *
     */
    public static function getMax($array, $countBackslash = false)
    {
        $lengths = array_map('strlen', array_keys($array));
        return max($lengths);
    }
    /**
     *
     */
    public static function getHeader()
    {
        return eme_autoloader_header();
    }
    /**
     *
     */
    public static function makeTab($length)
    {
        return str_repeat(' ', $length);
    }
    /**
     *
     */
    public function dump(string $file, string $contents)
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            // dir doesn't exist, make it
            mkdir($dir, 0777, true);
        }
        file_put_contents($file, $contents);
    }
}
