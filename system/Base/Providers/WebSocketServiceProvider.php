<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\WebSocketServiceProvider\Wss;

class WebSocketServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'wss',
            function () use ($container) {
                $config = $container->getShared('config');
                return (new Wss($config))->init();
            }
        );
    }
}