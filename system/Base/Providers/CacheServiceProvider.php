<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\CacheServiceProvider\ApcuCache;
use System\Base\Providers\CacheServiceProvider\OpCache;
use System\Base\Providers\CacheServiceProvider\StreamCache;
use System\Base\Providers\CacheServiceProvider\CacheTools;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'streamCache',
            function () use ($container) {
                return (new StreamCache($container))->initCache();
            }
        );

        $container->setShared(
            'apcuCache',
            function () use ($container) {
                return (new ApcuCache($container))->initCache();
            }
        );

        $container->setShared(
            'opCache',
            function () use ($container) {
                return (new OpCache($container))->initCache();
            }
        );

        $container->setShared(
            'cacheTools',
            function () use ($container) {
                return new CacheTools($container);
            }
        );
    }
}