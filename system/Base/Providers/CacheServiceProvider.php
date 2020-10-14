<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\CacheServiceProvider\ApcuCache;
use System\Base\Providers\CacheServiceProvider\CacheTools;
use System\Base\Providers\CacheServiceProvider\OpCache;
use System\Base\Providers\CacheServiceProvider\StreamCache;
use System\Base\Providers\CacheServiceProvider\ModelsMetadataCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $container->setShared(
            'streamCache',
            function () use ($container) {
                return (new StreamCache($container))->init();
            }
        );

        $container->setShared(
            'apcuCache',
            function () use ($container) {
                return (new ApcuCache($container))->init();
            }
        );

        $container->setShared(
            'opCache',
            function () use ($container) {
                return (new OpCache($container))->init();
            }
        );

        $container->setShared(
            'cacheTools',
            function () use ($container) {
                return new CacheTools($container);
            }
        );

        if ($container->getShared('config')->cache) {
            $container->setShared(
                'modelsMetadata',
                function () use ($container) {
                    return (new ModelsMetadataCache($container))->init();
                }
            );
        }
    }
}