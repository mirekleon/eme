<?php

namespace Eme\Core\FileSystem;

use Eme\Core\FileSystem\Exception\IOException;

use array_slice;
use func_get_args;
use set_error_handler;
use restore_error_handler;

/**
 *
 */
class Dir
{
    /**
     *
     */
    private $error;
    /**
     *
     */
    public function mkdir($path, $mode = 0777, $recursive = true)
    {
        if (!$this->sandbox('mkdir', $path, $mode, $recursive)) {
            throw new IOException(
                sprintf('Unable to create %s directory! %s', $path, $this->getLastError())
            );
        }
    }
    /**
     *
     */
    public function sandbox($function)
    {
        $this->error = null;
        set_error_handler(function ($type, $message) {
            $this->error = $message;
        });
        $result = $function(...array_slice(func_get_args(), 1));
        restore_error_handler();
        return $result;
    }
    /**
     *
     */
    public function getLastError()
    {
        return $this->error;
    }
}
