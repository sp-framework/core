<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\CacheServiceProvider\ApcuCache;
use System\Base\Providers\CacheServiceProvider\CacheTools;
use System\Base\Providers\CacheServiceProvider\Caching;
use System\Base\Providers\CacheServiceProvider\ModelsMetadataCache;
use System\Base\Providers\CacheServiceProvider\OpCache;
use System\Base\Providers\CacheServiceProvider\StreamCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(DiInterface $container) : void
    {
        $caches = [];

        $container->setShared(
            'streamCache',
            function () use ($container) {
                $cacheConfig = $container->getShared('config')->cache;
                return (new StreamCache($cacheConfig))->init();
            }
        );
        $caches['streamCache'] = $container->getShared('streamCache');

        if (extension_loaded('apcu')) {
            $container->setShared(
                'apcuCache',
                function () use ($container) {
                    $cacheConfig = $container->getShared('config')->cache;
                    return (new ApcuCache($cacheConfig))->init();
                }
            );
        } else {
            $container->setShared(
                'apcuCache',
                function () {
                    return false;
                }
            );
        }
        $caches['apcuCache'] = $container->getShared('apcuCache');

        if (extension_loaded('Zend OPcache')) {
            $container->setShared(
                'opCache',
                function () {
                    return (new OpCache())->init();
                }
            );
        } else {
            $container->setShared(
                'opCache',
                function () {
                    return false;
                }
            );
        }

        $container->setShared(
            'cacheTools',
            function () use ($container, $caches) {
                $cacheConfig = $container->getShared('config')->cache;
                $localContent = $container->getShared('localContent');
                $opCache = $container->getShared('opCache');
                $helper = $container->getShared('helper');
                return new CacheTools($cacheConfig, $caches, $localContent, $opCache, $helper);
            }
        );

        $container->setShared(
            'caching',
            function () use ($container, $caches) {
                $cacheConfig = $container->getShared('config')->cache;
                return new Caching($cacheConfig, $caches);
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