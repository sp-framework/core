<?php

namespace System\Base\Providers\ContentServiceProvider\Local;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Content
{
    public function __construct()
    {
        include (__DIR__ . '/vendor/autoload.php');
    }

    public function init(string $path = null, array $visibility = [])
    {
        return new Filesystem(
            new Local(base_path($path)),
            $visibility
        );
    }
}