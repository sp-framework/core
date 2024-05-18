<?php

namespace System\Base\Providers\CacheServiceProvider;

class Caching
{
    public $enabled = false;

    public $cache;

    protected $cacheConfig;

    protected $caches;

    public function __construct($cacheConfig, array $caches)
    {
        $this->cacheConfig = $cacheConfig;

        $this->caches = $caches;
    }

    public function init($cacheService, $cacheTimeout = 3600)
    {
        if ($this->enabled) {
            return false;
        }

        if (!isset($this->caches[$cacheService])) {
            return false;
        }

        if ($this->cacheConfig->enabled === false) {
            $this->cache = $this->caches[$cacheService]->init(true, $cacheTimeout);
        } else {
            $this->cache = $this->caches[$cacheService];
        }

        $this->enabled = false;

        return $this;
    }

    public function getCache($cacheKey)
    {
        if ($this->cache->has($cacheKey)) {
            return (array) $this->cache->get($cacheKey);
        }

        return false;
    }

    public function setCache(string $cacheKey, $data)
    {
        if ($this->cache->set($cacheKey, $data)) {
            return $this->getCache($cacheKey);
        }

        return false;
    }
}