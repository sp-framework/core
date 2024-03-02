<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Assets\Manager;

class Assets
{
    protected $assets;

    protected $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function init()
    {
        $this->assets = new Manager($this->tag);

        return $this->assets;
    }
}