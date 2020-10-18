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

    public function init()
    {
        if ($this->cacheConfig->enabled) {
            $serializerFactory = new SerializerFactory();

            $options = [
                'defaultSerializer' => 'Json',
                'lifetime'          => $this->cacheConfig->timeout
            ];

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
                    'prefix'            => 'db',
                    'storageDir'        => $savePath
                ],
            ];

            $this->cache = $cacheFactory->load($cacheOptions);

            return $this->cache;
        } else {
            return false;
        }
    }

    protected function checkCachePath()
    {
        if (!is_dir(base_path('var/storage/cache/'))) {
            if (!mkdir(base_path('var/storage/cache/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}