<?php

namespace System\Base\Providers\SessionServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Session\Manager;

class Session
{
    private $container;

    protected $session;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        include('../system/Base/Helpers.php');

        $this->session = new Manager();

        if ($this->checkCachePath()) {
            $savePath = base_path('var/storage/cache/session/');
        } else {
            $savePath = '/tmp/';
        }

        $sessionFiles = new Stream(
            [
                'savePath'  => $savePath
            ]
        );

        $this->session->setAdapter($sessionFiles);

        return $this->session;
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/session/'))) {
            if (!mkdir(base_path('var/storage/cache/session/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}