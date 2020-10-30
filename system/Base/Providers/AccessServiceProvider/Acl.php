<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Acl\Adapter\Memory;

class Acl
{
    protected $acl;

    public function __construct()
    {

    }

    public function init()
    {
        $this->acl = new Memory();

        return $this->acl;
    }
}