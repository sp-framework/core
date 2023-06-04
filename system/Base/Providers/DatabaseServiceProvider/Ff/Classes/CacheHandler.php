<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use System\Base\Providers\DatabaseServiceProvider\Ff\Cache;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\QueryBuilder;

class CacheHandler
{
    protected $cache;

    protected $cacheTokenArray;

    protected $regenerateCache;

    protected $useCache;

    public function __construct(string $storePath, QueryBuilder $queryBuilder)
    {
        $this->cacheTokenArray = $queryBuilder->getCacheTokenArray();

        $queryBuilderProperties = $queryBuilder->getConditionProperties();

        $this->useCache = $queryBuilderProperties["useCache"];

        $this->regenerateCache = $queryBuilderProperties["regenerateCache"];

        $this->cache = new Cache($storePath, $this->getCacheTokenArray(), $queryBuilderProperties["cacheLifetime"]);
    }

    public function getCache(): Cache
    {
        return $this->cache;
    }

    public function getCacheContent($getOneDocument)
    {
        if ($this->useCache !== true) {
            return null;
        }

        $this->updateCacheTokenArray(['oneDocument' => $getOneDocument]);

        if ($this->regenerateCache === true) {
            $this->getCache()->delete();
        }

        $cacheResults = $this->getCache()->get();

        if (is_array($cacheResults)) {
            return $cacheResults;
        }

        return null;
    }

    public function setCacheContent(array $results)
    {
        if ($this->useCache === true) {
            $this->getCache()->set($results);
        }
    }

    public function deleteAllWithNoLifetime(): bool
    {
        return $this->getCache()->deleteAllWithNoLifetime();
    }

    public function &getCacheTokenArray(): array
    {
        return $this->cacheTokenArray;
    }

    protected function updateCacheTokenArray(array $tokenUpdate)
    {
        if (empty($tokenUpdate)) {
            return;
        }

        $cacheTokenArray = $this->getCacheTokenArray();

        foreach ($tokenUpdate as $key => $value) {
            $cacheTokenArray[$key] = $value;
        }

        $this->cacheTokenArray = $cacheTokenArray;
    }
}