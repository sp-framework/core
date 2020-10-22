<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\HttpServiceProvider\Cookies;
use System\Base\Providers\HttpServiceProvider\Request;
use System\Base\Providers\HttpServiceProvider\Response;
use System\Base\Providers\HttpServiceProvider\Links;

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

        $container->setShared(
            'cookies',
            function () {
                return (new Cookies())->init();
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
