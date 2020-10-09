<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Di\DiInterface;

class OpCache
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
            //https://medium.com/@dylanwenzlau/500x-faster-caching-than-redis-memcache-apc-in-php-hhvm-dcd26e8447ad
            //
            //Tried and tested, works great.

            return false
        } else {
            return false;
        }
    }
}