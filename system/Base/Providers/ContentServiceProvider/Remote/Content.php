<?php

namespace System\Base\Providers\ContentServiceProvider\Remote;

use System\Base\Providers\ContentServiceProvider\Remote\Content\Dropbox;
use System\Base\Providers\ContentServiceProvider\Remote\Content\Ftp;
use System\Base\Providers\ContentServiceProvider\Remote\Content\Sftp;

class Content
{
    protected $dropbox;

    protected $ftp;

    protected $sftp;

    public function __construct()
    {
    }

    public function init()
    {
        return $this;
    }

    // Do manager instead
// https://flysystem.thephpleague.com/docs/advanced/mount-manager/

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

    protected function initFtp()
    {
        $this->ftp = (new Ftp())->init();

        return $this->ftp;
    }

    protected function initSftp()
    {
        $this->sftp = (new Sftp())->init();

        return $this->sftp;
    }
}