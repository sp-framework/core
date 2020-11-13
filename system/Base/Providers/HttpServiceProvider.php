<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Helper\Json;
use System\Base\Providers\HttpServiceProvider\Cookies;
use System\Base\Providers\HttpServiceProvider\Links;
use System\Base\Providers\HttpServiceProvider\Request;
use System\Base\Providers\HttpServiceProvider\Response;

class HttpServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'request',
            function () {
                return (new Request())->init();
            }
        );

        $container->setShared(
            'response',
            function () {
                return (new Response())->init();
            }
        );

        $response = $container->getShared('response');
        $crypt = $container->getShared('crypt');
        $random = $container->getShared('random');
        $core = $container->getShared('modules')->core;

        $container->setShared(
            'cookies',
            function () use ($response, $crypt, $random, $core) {
                return (new Cookies($response, $crypt, $random, $core))->init();
            }
        );

        $request = $container->getShared('request');
        $application = $container->getShared('modules')->applications->getApplicationInfo();
        $view = $container->getShared('modules')->views->getViewInfo();

        $container->setShared(
            'links',
            function () use ($request, $application, $view) {
                return new Links($request, $application, $view);
            }
        );
    }
}
