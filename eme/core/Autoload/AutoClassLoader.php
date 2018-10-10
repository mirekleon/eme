<?php

/**
 * This file is part of Eme.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eme\Core\Autoload;

use Eme\Core\Autoload\ClassMapGenarator;
use Eme\Core\Autoload\Exception\AutoLoadException;
use Eme\Core\Autoload\Exception\InvalidArgumentException;

/**
 * Autoload class
 */
class AutoClassLoader
{
    /**
     * @access private
     *
     * @var Array
     */
    private $namespaces = [];
    /**
     * @access private
     *
     * @var Array
     */
    private $classMap = [];
    /**
     * @access public
     * Get registered classes
     *
     * @return Array
     */
    public function getClassMap()
    {
        return $this->classMap;
    }
    /**
     * @access public
     * Add class map
     *
     * @param Array $classMap - mapped classes
     * @return Void
     */
    public function addClassMap(array $classMap = [])
    {
        $this->classMap = array_merge($this->classMap, $classMap);
    }
    /**
     * @access public
     * Register class loader
     *
     * @param Boolean $prepend - prepend path
     * @return Void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }
    /**
     * @access public
     * Unregister class loader
     *
     * @return Void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
    /**
     * @access public
     * Load class
     *
     * @param String $class - class name
     * @return Boolean
     * @throws AutoLoadException
     */
    public function loadClass($class)
    {
        if ($file = $this->getFile($class)) {
            loadFile($file);
            return true;
        }

        $classMap = dump_autoloader(CLASS_MAP_PATHS);
        $this->addClassMap($classMap);
        // give one last try, dump classes and search again before throw the exception
        if ($file = $this->getFile($class)) {
            loadFile($file);
            return true;
        }

        throw new AutoLoadException("Class {$class} not found");
    }
    /**
     * @access public
     * Add namespace
     *
     * @param String $namespace - namespace
     * @param String $path - namespace
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addNamespace($namespace, $path)
    {
        if (trim($namespace, '\\') === '') {
            throw new InvalidArgumentException('Namespace cannot be empty');
        }

        if ('\\' !== $namespace[strlen($namespace) -1]) {
            throw new InvalidArgumentException('Namespace must end with a namespace separator');
        }

        $this->namespace[ltrim($namespace, '\\')] = $path;
        return $this;
    }
    /**
     * @access public
     * Get file
     *
     * @param String $class - file path
     * @return Mixed
     */
    public function getFile($class)
    {
        $file = $this->findFile($class);
        return $file;
    }
    /**
     * @access public
     * Search for class
     *
     * @param String $class - file path
     * @return Mixed
     */
    public function findFile($class)
    {

        $index = $class[0];

        if (isset($this->classMap[SINGLE_CLASS_IDENTIFIER][$class])) {
            return $this->classMap[SINGLE_CLASS_IDENTIFIER][$class];
        }

        if (!isset($this->classMap[$index])) {
            return false;
        }

        if (false !== $pos = strrpos($class, '\\')) {
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 0, $pos)).DIRECTORY_SEPARATOR;
            $className = substr($class, $pos + 1);
        }

        $dirs = array_keys($this->classMap[$index]);
        $file = null;
        while ($dirs && !$file) {
            $namespace = array_shift($dirs);
            if ($classPath === $namespace) {
                $candidate = $this->classMap[$index][$namespace].DIRECTORY_SEPARATOR.$className.'.php';
                if (file_exists($candidate)) {
                    $file = $candidate;
                }
            }
        }
        return $file;
    }
}
/**
 * Include file
 */
if (!function_exists('loadFile')) {
    function loadFile($file)
    {
        // do not expose error here
        @include $file;
    }
}
