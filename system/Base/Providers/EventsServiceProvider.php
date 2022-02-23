<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\EventsServiceProvider\Events;

class EventsServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'events',
            function () use ($container) {
                return (new Events($container))->init();
            }
        );
    }
}