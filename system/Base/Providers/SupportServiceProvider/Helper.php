<?php

namespace System\Base\Providers\SupportServiceProvider;

use Phalcon\Support\HelperFactory as PhalconHelper;

class Helper
{
    protected $phalconHelper;

    public function __construct()
    {
        //
    }

    public function init()
    {
        $this->phalconHelper = new PhalconHelper();

        return $this->phalconHelper;
    }
}