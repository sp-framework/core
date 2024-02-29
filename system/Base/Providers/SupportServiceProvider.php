<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\SupportServiceProvider\Collection;
use System\Base\Providers\SupportServiceProvider\Debug;
use System\Base\Providers\SupportServiceProvider\Helper;
use System\Base\Providers\SupportServiceProvider\Registry;

class SupportServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'helper',
            function () {
                return (new Helper())->init();
            }
        );

        $container->setShared(
            'collection',
            function () {
                return (new Collection())->init();
            }
        );

        $container->setShared(
            'debug',
            function () {
                return (new Debug())->init();
            }
        );

        $container->setShared(
            'registry',
            function () {
                return (new Registry())->init();
            }
        );
    }
}