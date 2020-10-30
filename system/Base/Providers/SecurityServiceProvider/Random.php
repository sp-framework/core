<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Security\Random as PhalconRandom;

class Random
{
    protected $connectionId;

    public function __construct()
    {
    }

    public function init()
    {
        return new PhalconRandom();
    }
}