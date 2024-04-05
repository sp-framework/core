<?php

namespace System\Base\Providers\EventsServiceProvider;

use Phalcon\Events\Manager as EventsManager;
use System\Base\Providers\MicroMiddlewaresServiceProvider;

class MicroEvents
{
    protected $events;

    public function __construct()
    {
    }

    public function init()
    {
        $this->events = new EventsManager();

        $this->registerMicroMiddlewareServiceProvider();

        return $this->events;
    }

    protected function registerMicroMiddlewareServiceProvider()
    {
        $this->events->attach(
            'micro:beforeExecuteRoute',
            new MicroMiddlewaresServiceProvider()
        );

        $this->events->attach(
            'micro:afterExecuteRoute',
            new MicroMiddlewaresServiceProvider()
        );
    }
}