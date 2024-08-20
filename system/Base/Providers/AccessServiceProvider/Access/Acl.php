<?php

namespace System\Base\Providers\AccessServiceProvider\Access;

use Phalcon\Acl\Adapter\Memory;
use System\Base\BasePackage;

class Acl extends BasePackage
{
    protected $acl;

    public function init()
    {
        $this->acl = new Memory();

        return $this->acl;
    }
}