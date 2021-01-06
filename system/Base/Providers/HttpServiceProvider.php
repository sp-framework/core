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


        $container->setShared(
            'cookies',
            function () use ($container) {
                $response = $container->getShared('response');
                $crypt = $container->getShared('crypt');
                $random = $container->getShared('random');
                $core = $container->getShared('modules')->core;
                return (new Cookies($response, $crypt, $random, $core))->init();
            }
        );

        $container->setShared(
            'links',
            function () use ($container) {
                $request = $container->getShared('request');
                $application = $container->getShared('modules')->applications->getApplicationInfo();
                $view = $container->getShared('modules')->views->getViewInfo();
                $domain = $container->getShared('basepackages')->domains->getDomain();
                return new Links($request, $application, $view, $domain);
            }
        );
    }
}
