<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Security as PhalconSecurity;

class Security
{
    protected $connectionId;

    public function __construct()
    {
    }

    public function init()
    {
        return new PhalconSecurity();
    }
}