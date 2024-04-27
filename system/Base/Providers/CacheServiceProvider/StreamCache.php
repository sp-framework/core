<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;

class StreamCache
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

            $adapter = new AdapterFactory($serializerFactory, $options);

            $cacheFactory = new CacheFactory($adapter);

            if ($this->checkCachePath()) {
                $savePath = base_path('var/storage/cache/');
            } else {
                $savePath = '/tmp/';
            }

            $cacheOptions = [
                'adapter'   => 'stream',
                'options'   => [
                    'prefix'            => 'stream',
                    'storageDir'        => $savePath,
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

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/stream/'))) {
            if (!mkdir(base_path('var/storage/cache/stream/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}