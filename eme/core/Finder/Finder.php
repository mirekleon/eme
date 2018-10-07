<?php

namespace Eme\Core\Finder;

use file_exists;
use array_merge;
use preg_replace;
use RegexIterator;
use Eme\Core\Finder\FileInfo;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 *
 */
class Finder implements \IteratorAggregate
{
    /**
     *
     */
    const SYSTEM_SEPARATOR = 1;
    /**
     *
     */
    private $collection = [];
    /**
     *
     */
    private $options = 0;
    /**
     *
     */
    public static $separator = '/';
    /**
     *
     */
    private $paths = [];
    /**
     *
     */
    public function __construct($options = 0)
    {
        $this->options = $options;
        if ($this->options === 1) {
            static::$separator = \DIRECTORY_SEPARATOR;
        }
    }
    /**
     *
     */
    public function exists($path)
    {
        return file_exists($path);
    }
    /**
     *
     */
    public function paths($paths)
    {
        foreach ((array) $paths as $path) {
            $this->paths[] = self::normalizePath($path);
        }
        return $this;
    }
    /**
     *
     */
    public static function normalizePath($path)
    {
        return preg_replace('/\\\\|\//', static::$separator, $path);
    }
    /**
     *
     */
    public function search($pattern = '/.*/')
    {
        $flags = RecursiveDirectoryIterator::SKIP_DOTS;
        foreach ($this->paths as $path) {
            $dir = new RecursiveDirectoryIterator($path, $flags);
            $ite = new RecursiveIteratorIterator($dir);
            $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
            foreach ($files as $file) {
                $this->collection = array_merge($this->collection, $file);
            }
        }
    }
    /**
     *
     */
    public function getIterator()
    {
        foreach ($this->collection as $item) {
            yield new FileInfo(Finder::normalizePath($item));
        }
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
    public function count()
    {
        return iterator_count($this->getIterator());
    }
}
