<?php

namespace System\Base\Providers\FileSystemServiceProvider;

use Phalcon\Di\DiInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class File
{
    private $container;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        return new Filesystem(new Local(base_path()));
    }
}