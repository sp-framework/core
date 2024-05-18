<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Html\TagFactory as PhalconTag;

class Tag
{
    protected $tag;

    protected $escaper;

    public function __construct($escaper)
    {
        $this->escaper = $escaper;
    }

    public function init()
    {
        $this->tag = new PhalconTag($this->escaper);

        return $this->tag;
    }
}