<?php

/**
 * This file is part of Eme.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eme\Core\Autoload;

use Eme\Core\Finder\Finder;
use Eme\Core\Autoload\Exception\AutoLoadException;

/**
 *
 */
class ClassMapGenarator
{
    /**
     *
     */
    private $map = [];
    /**
     *
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        return $this;
    }
    /**
     *
     */
    public function getPaths()
    {
        return $this->paths;
    }
    /**
     *
     */
    public function generateClassMap()
    {
        $finder = new Finder();
        $finder->paths($this->getPaths())->search('/.*.php/');
        foreach ($finder as $file) {
            $classes = $this->findClasses($file->getPathName());
            if ($classes) {
                foreach ($classes as $namespace => $class) {
                    if (!$namespace) {
                        $path = realpath($file->getPathName());
                        $index = $this->map[SINGLE_CLASS_IDENTIFIER][$class] ?? null;
                        if ($index) {
                            throw new AutoLoadException(
                                sprintf(
                                    'Duplicate class %s in different paths [%s]',
                                    $class,
                                    join(', ', [$index, $path])
                                )
                            );
                        }
                        $this->map[SINGLE_CLASS_IDENTIFIER][$class] = $path;
                    } else {
                        $this->map[$namespace[0]][$namespace] = realpath($file->getDirName());
                    }
                }
            }
        }
        // sort
        foreach ($this->map as $key => $map) {
            natsort($map);
            $this->map[$key] = $map;
        }
        return $this;
    }
    /**
     *
     */
    public function getClassMap()
    {
        return $this->map;
    }
    /**
     *
     */
    private static function findClasses($path)
    {
        $contents = file_get_contents($path);
        $tokens = token_get_all($contents);
        $classes = array();
        $namespace = '';
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }
            $class = '';
            switch ($token[0]) {
                case T_NAMESPACE:
                    $namespace = '';
                    // If there is a namespace, extract it
                    while (isset($tokens[++$i][1])) {
                        if (in_array($tokens[$i][0], array(T_STRING, T_NS_SEPARATOR))) {
                            $namespace .= $tokens[$i][1];
                        }
                    }
                    $namespace .= '\\';
                    break;
                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    // Skip usage of ::class constant
                    $isClassConstant = false;
                    for ($j = $i - 1; $j > 0; --$j) {
                        if (!isset($tokens[$j][1])) {
                            break;
                        }
                        if (T_DOUBLE_COLON === $tokens[$j][0]) {
                            $isClassConstant = true;
                            break;
                        } elseif (!in_array($tokens[$j][0], array(T_WHITESPACE, T_DOC_COMMENT, T_COMMENT))) {
                            break;
                        }
                    }
                    if ($isClassConstant) {
                        break;
                    }
                    // Find the classname
                    while (isset($tokens[++$i][1])) {
                        $t = $tokens[$i];
                        if (T_STRING === $t[0]) {
                            $class .= $t[1];
                        } elseif ('' !== $class && T_WHITESPACE === $t[0]) {
                            break;
                        }
                    }
                    $classes[$namespace] = ltrim($class, '\\');
                    break;
                default:
                    break;
            }
        }
        return $classes;
    }
}
