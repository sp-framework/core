<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Di\DiInterface;

class ApcuCache
{
    private $container;

    protected $cache;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function initCache()
    {
        if ($this->container->getShared('config')->cache) {
            $options = [
                'defaultSerializer' => 'Json',
                'lifetime'          => $this->container->getShared('config')->cacheTimeout
            ];

            $serializerFactory = new SerializerFactory();

            $adapterFactory    = new AdapterFactory($serializerFactory, $options);

            $cacheFactory = new CacheFactory($adapterFactory);

            $adapter = $adapterFactory->newInstance('apcu', $options);

            $cacheOptions = [
                'adapter' => 'apcu',
                'options' => [
                    'prefix' => 'sp-',
                ],
            ];

            $this->cache = $cacheFactory->load($cacheOptions);

            return $this->cache;
        } else {
            return false;
        }
    }
}