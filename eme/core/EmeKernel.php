<?php

namespace Eme\Core;

/**
 *
 */
class EmeKernel
{
    /**
     *
     */
    public function handle()
    {
        //
    }
    /**
     *
     */
    public function send()
    {
        //
    }
    /**
     *
     */
    public function end()
    {
        echo sprintf('<!-- eme HTTP 200 OK %s -->', date('Y'));
    }
}
