<?php

namespace Eme\Core\Finder;

use Eme\Core\Finder\Finder;

/**
 *
 */
class FileInfo extends \SplFileInfo
{
    /**
     *
     */
    private $path;
    /**
     *
     */
    public function __construct($path)
    {
        parent::__construct($path);
        $this->path = Finder::normalizePath(realpath($path));
        $this->dirName = Finder::normalizePath(dirname($path));
    }
    /**
     *
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     *
     */
    public function getDirName()
    {
        return $this->dirName;
    }
}
