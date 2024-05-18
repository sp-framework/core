<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Encryption\Security\Random as PhalconRandom;

class Random
{
    protected $random;

    public function __construct()
    {
    }

    public function init()
    {
        $this->random = new PhalconRandom();

        return $this->random;
    }
}