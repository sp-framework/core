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
        $cacheConfig = $container->getShared('config')->cache;

        $container->setShared(
            'streamCache',
            function () use ($cacheConfig) {
                return (new StreamCache($cacheConfig))->init();
            }
        );

        $container->setShared(
            'apcuCache',
            function () use ($cacheConfig) {
                return (new ApcuCache($cacheConfig))->init();
            }
        );

        $container->setShared(
            'opCache',
            function () use ($cacheConfig) {
                return (new OpCache($cacheConfig))->init();
            }
        );

        $caches = [];
        $caches['streamCache'] = $container->getShared('streamCache');
        $caches['apcuCache'] = $container->getShared('apcuCache');
        $caches['opCache'] = $container->getShared('opCache');


        $container->setShared(
            'cacheTools',
            function () use ($cacheConfig, $caches) {
                return new CacheTools($cacheConfig, $caches);
            }
        );

        if ($container->getShared('config')->cache) {
            $container->setShared(
                'modelsMetadata',
                function () {
                    return (new ModelsMetadataCache())->init();
                }
            );
        }
    }
}