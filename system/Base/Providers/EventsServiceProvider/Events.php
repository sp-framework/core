<?php

namespace System\Base\Providers\EventsServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use System\Base\Providers\MiddlewaresServiceProvider;

class Events
{
    protected $container;

    protected $events;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        $this->events = new EventsManager();

        $this->events->enablePriorities(true);

        $this->events->collectResponses(true);

        $this->registerMiddlewareServiceProvider();

        return $this->events;
    }

    protected function registerMiddlewareServiceProvider()
    {
        $this->events->attach(
            'dispatch:beforeExecuteRoute',
            new MiddlewaresServiceProvider()
        );
    }
}