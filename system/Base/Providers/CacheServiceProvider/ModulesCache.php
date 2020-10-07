<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Config\Adapter\Json;
use Phalcon\Di\DiInterface;
use Phalcon\Storage\SerializerFactory;

class ModulesCache
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
            $serializerFactory = new SerializerFactory();

            $options = [
                'defaultSerializer' => 'Json',
                'lifetime'          => $this->container->getShared('config')->cacheTimeout
            ];

            $adapter    = new AdapterFactory($serializerFactory, $options);

            $cacheFactory = new CacheFactory($adapter);

            $cacheOptions = [
                'adapter'   => 'stream',
                'options'   => [
                    'prefix'            => 'modules',
                    'storageDir'        => base_path('var/storage/cache/')
                ],
            ];

            $this->cache = $cacheFactory->load($cacheOptions);

            return $this->cache;
        } else {
            return false;
        }
    }
}