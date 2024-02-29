<?php

namespace System\Base\Providers\SupportServiceProvider;

use Phalcon\Support\Registry as PhalconRegistry;

class Registry
{
    protected $phalconRegistry;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->phalconRegistry = new PhalconRegistry();

        return $this->phalconRegistry;
    }
}