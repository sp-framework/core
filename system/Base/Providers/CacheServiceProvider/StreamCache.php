<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;

class StreamCache
{
    private $container;

    protected $cache;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        if ($this->container->getShared('config')->cache) {
            $serializerFactory = new SerializerFactory();

            $options = [
                'defaultSerializer' => 'Json',
                'lifetime'          => $this->container->getShared('config')->cacheTimeout
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