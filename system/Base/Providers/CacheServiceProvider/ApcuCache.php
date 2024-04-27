<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;

class ApcuCache
{
    protected $cache;

    protected $cacheConfig;

    public function __construct($cacheConfig)
    {
        $this->cacheConfig = $cacheConfig;
    }

    public function init($force = false, $timeout = null)
    {
        if ($this->cacheConfig->enabled || $force) {
            $options = [
                'defaultSerializer' => 'json',
                'lifetime'          => $timeout ?? $this->cacheConfig->timeout
            ];

            $serializerFactory = new SerializerFactory();

            $adapterFactory = new AdapterFactory($serializerFactory, $options);

            $cacheFactory = new CacheFactory($adapterFactory);

            $adapter = $adapterFactory->newInstance('apcu', $options);

            $cacheOptions = [
                'adapter' => 'apcu',
                'options' => [
                    'prefix'            => 'sp-',
                    'defaultSerializer' => 'json',
                    'lifetime'          => $timeout ?? $this->cacheConfig->timeout
                ],
            ];

            $this->cache = $cacheFactory->load($cacheOptions);

            return $this->cache;
        } else {
            return $this;
        }
    }
}