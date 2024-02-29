<?php

namespace System\Base\Providers\SupportServiceProvider;

use Phalcon\Support\Debug as PhalconDebug;

class Debug
{
    protected $phalconDebug;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->phalconDebug = new PhalconDebug();

        return $this->phalconDebug;
    }
}