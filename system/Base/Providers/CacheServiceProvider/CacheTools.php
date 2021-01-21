<?php

namespace System\Base\Providers\CacheServiceProvider;

class CacheTools
{
	protected $cacheTimeout;

	protected $cacheService;

	protected $cache;

	protected $cacheConfig;

	public function __construct($cacheConfig, array $caches)
	{
		$this->cacheConfig = $cacheConfig;

		if ($this->cacheConfig->timeout && $this->cacheConfig->service) {
			$this->cacheTimeout = $this->cacheConfig->timeout;
			$this->cacheService = $this->cacheConfig->service;
		} else {
			$this->cacheTimeout = 3600;// Default seconds
			$this->cacheService = 'streamCache';
		}

		$this->cache = $caches[$this->cacheService];
	}

	public function addModelCacheParameters($parameters = null, $cacheName = null)
	{
		if ($parameters && $cacheName) {
			$cacheKey[0] = $cacheName;
			$cacheKey[] = $parameters;
		} else if (!$parameters && $cacheName) {
			$cacheKey[0] = $cacheName;
		} else if ($parameters && !cacheName) {
			$cacheKey = $parameters;
		}

		if (!isset($parameters['cache'])) {
			$parameters['cache'] = [
				'key'      	=> $this->generateCacheKey($cacheKey),
				'lifetime' 	=> $this->cacheTimeout,
				'service' 	=> $this->cacheService,
			];
		}

		return $parameters;
	}

	public function generateCacheKey($cacheKey)
	{
		$uniqueKey = [];
		foreach ($cacheKey as $key => $value) {
			if (true === is_scalar($value)) {
				$uniqueKey[] = $value;
			} elseif (true === is_array($value)) {
				$uniqueKey[] = sprintf(
					'%s-%s',
					$key,
					$this->generateCacheKey($value)
				);
			}
		}

		$key =
			str_replace(
				' ', '', str_replace(//remove Space
					':', '', str_replace(//Remove Colon
						'=', '-', join('-', $uniqueKey)//remove equals
					)
				)
			);

		return $key;
	}

	public function deleteCache($cacheKey)
	{
		// if (!is_array($cacheKey)) {
		// 	$cacheKey = [$cacheKey];
		// }

		// $key = $this->generateCacheKey($cacheKey);

		if ($this->cache->has($cacheKey)) {
			$this->cache->delete($cacheKey);
		}
	}

	public function getCache($cacheKey)
	{
		if ($this->cacheConfig->enabled) {
			if ($this->cache->has($cacheKey)) {
				return $this->cache->get($cacheKey);
			}
		}

		return null;
	}

	public function setCache(string $cacheKey, array $data)
	{
		if ($this->cacheConfig->enabled) {
			$this->cache->set($cacheKey, $data);

			return true;
		}
	}
}