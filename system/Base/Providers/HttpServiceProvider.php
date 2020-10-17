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
            function () {
                return (new RequestService())->init();
            }
        );

        $container->setShared(
            'response',
            function () {
                return (new ResponseService())->init();
            }
        );

        $container->setShared(
            'cookies',
            function () {
                return (new CookiesService())->init();
            }
        );
    }
}
