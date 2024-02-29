<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Html\Escaper as PhalconEscaper;

class Escaper
{
    protected $escaper;

    public function __construct()
    {
    }

    public function init()
    {
        $this->escaper = new PhalconEscaper;

        return $this->escaper;
    }
}