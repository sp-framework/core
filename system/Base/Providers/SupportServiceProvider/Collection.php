<?php

namespace System\Base\Providers\SupportServiceProvider;

use Phalcon\Support\Collection as PhalconCollection;

class Collection
{
    protected $phalconCollection;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->phalconCollection = new PhalconCollection();

        return $this->phalconCollection;
    }
}