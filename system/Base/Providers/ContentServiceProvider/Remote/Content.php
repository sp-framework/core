<?php

namespace System\Base\Providers\ContentServiceProvider\Local;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use System\Base\Providers\ContentServiceProvider\Remote\Dropbox;

class Content
{
    protected $dropbox;

    protected $ftp;

    public function __construct()
    {
    }

    public function init()
    {
        return $this;
    }

    public function __get($name)
    {
        if (!isset($this->{$name})) {
            if (method_exists($this, $method = "init" . ucfirst("{$name}"))) {
                $this->{$name} = $this->{$method}();
            }
        }

        return $this->{$name};
    }

    protected function initDropbox()
    {
        $this->dropbox = (new Dropbox())->init();

        return $this->dropbox;
    }
}