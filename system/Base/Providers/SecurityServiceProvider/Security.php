<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Encryption\Security as PhalconSecurity;

class Security
{
    public $security;

    public function __construct()
    {
    }

    public function init(int $workFactor = 12)
    {
        $this->security = new PhalconSecurity();

        $this->security->setWorkFactor($workFactor);

        return $this->security;
    }
}