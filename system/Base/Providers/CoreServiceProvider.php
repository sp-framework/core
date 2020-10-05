<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Http\Cookie;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Http\Response\Cookies;
use Phalcon\Session\Manager;
use Phalcon\Url;
use System\Base\Providers\RouteServiceProvider\Router;

class CoreServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'url',
            function () {
                return new Url();
            }
        );

        $container->setShared(
            'route',
            function () {
                return new Router();
            }
        );

        $container->setShared(
            'request',
            function () {
                return new Request();
            }
        );

        $container->setShared(
            'response',
            function () {
                return new Response();
            }
        );

        $container->setShared(
            'cookies',
            function () {
                return new Cookies();
            }
        );

        $container->setShared(
            'session',
            function () {
                return new Manager();
            }
        );
    }
}
