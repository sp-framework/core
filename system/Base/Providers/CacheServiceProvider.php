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
                $cacheConfig = $container->getShared('config')->cache;
                return (new StreamCache($cacheConfig))->init();
            }
        );

        $container->setShared(
            'apcuCache',
            function () use ($container) {
                $cacheConfig = $container->getShared('config')->cache;
                return (new ApcuCache($cacheConfig))->init();
            }
        );

        $container->setShared(
            'opCache',
            function () use ($container) {
                $cacheConfig = $container->getShared('config')->cache;
                return (new OpCache($cacheConfig))->init();
            }
        );

        $container->setShared(
            'cacheTools',
            function () use ($container) {
                $cacheConfig = $container->getShared('config')->cache;
                $caches = [];
                $caches['streamCache'] = $container->getShared('streamCache');
                $caches['apcuCache'] = $container->getShared('apcuCache');
                $caches['opCache'] = $container->getShared('opCache');
                $localContent = $container->getShared('localContent');
                return new CacheTools($cacheConfig, $caches, $localContent);
            }
        );

        if ($container->getShared('config')->cache->enabled) {
            $container->setShared(
                'modelsMetadata',
                function () {
                    return (new ModelsMetadataCache())->init();
                }
            );
        }
    }
}