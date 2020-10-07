<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\CacheServiceProvider\ModelsCache;
use System\Base\Providers\CacheServiceProvider\ModulesCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'modelsCache',
            function () use ($container) {
                return (new ModelsCache($container))->initCache();
            }
        );

        $container->setShared(
            'modulesCache',
            function () use ($container) {
                return (new ModulesCache($container))->initCache();
            }
        );
    }
}