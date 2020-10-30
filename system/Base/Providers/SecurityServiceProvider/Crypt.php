<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Crypt as PhalconCrypt;

class Crypt
{
    protected $connectionId;

    public function __construct()
    {
    }

    public function init()
    {
        return new Crypt();
    }
}