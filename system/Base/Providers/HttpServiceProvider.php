<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\HttpServiceProvider\RequestService;
use System\Base\Providers\HttpServiceProvider\ResponseService;
use System\Base\Providers\HttpServiceProvider\CookiesService;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'request',
            function () use ($container) {
                return (new RequestService($container))->init();
            }
        );

        $container->setShared(
            'response',
            function () use ($container) {
                return (new ResponseService($container))->init();
            }
        );

        $container->setShared(
            'cookies',
            function () use ($container) {
                return (new CookiesService($container))->init();
            }
        );
    }
}
