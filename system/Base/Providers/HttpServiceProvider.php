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
                $secTools = $container->getShared('secTools');
                return (new Cookies($response, $secTools))->init();
            }
        );

        $container->setShared(
            'links',
            function () use ($container) {
                $request = $container->getShared('request');
                $app = $container->getShared('apps')->getAppInfo();
                $view = $container->getShared('modules')->views->getViewInfo();
                $domain = $container->getShared('domains')->getDomain();
                return new Links($request, $app, $view, $domain);
            }
        );
    }
}
