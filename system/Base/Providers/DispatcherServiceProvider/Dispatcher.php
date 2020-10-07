<?php

namespace System\Base\Providers\DispatcherServiceProvider;

use Phalcon\Config\Adapter\Grouped;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher as PhalconDispatcher;

class Dispatcher
{
    private $container;

    protected $dispatcher;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->dispatcher = new PhalconDispatcher();

        $this->dispatcher->setControllerSuffix('Component');

        $this->dispatcher->setDefaultAction('view');
    }

    public function dispatch()
    {
        return $this->dispatcher;
    }
}