<?php

namespace Eme\Core\Autoload;

use Eme\Core\Autoload\ClassMapGenarator;
use Eme\Core\Autoload\Exception\AutoLoadException;
use Eme\Core\Autoload\Exception\InvalidArgumentException;

/**
 *
 */
class AutoClassLoader
{
    /**
     *
     */
    private $namespaces = [];
    /**
     *
     */
    private $classMap = [];
    /**
     *
     */
    public function getClassMap()
    {
        return $this->classMap;
    }
    /**
     *
     */
    public function addClassMap(array $classMap = [])
    {
        $this->classMap = $classMap;
    }
    /**
     *
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }
    /**
     *
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
    /**
     *
     */
    public function loadClass($class)
    {
        if ($file = $this->getFile($class)) {
            loadFile($file);
            return true;
        }

        $classMap = dump_autoloader(CLASS_MAP_PATHS);
        $this->addClassMap($classMap);
        // give one last try, dump classes and check before throw the exception
        if ($file = $this->getFile($class)) {
            loadFile($file);
            return true;
        }

        throw new AutoLoadException("Class {$class} not found");
    }
    /**
     *
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
     *
     */
    public function getFile($class)
    {
        $file = $this->findFile($class);
        return $file;
    }
    /**
     *
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
 *
 */
if (!function_exists('loadFile')) {
    function loadFile($file)
    {
        include $file;
    }
}
