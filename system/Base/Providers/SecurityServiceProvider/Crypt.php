<?php

namespace System\Base\Providers\SecurityServiceProvider;

use Phalcon\Encryption\Crypt as PhalconCrypt;

class Crypt
{
    protected $crypt;

    public function __construct()
    {
    }

    public function init()
    {
        $this->crypt = new PhalconCrypt();

        return $this->crypt;
    }
}