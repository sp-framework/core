<?php

namespace System\Base\Providers\CacheServiceProvider;

use Phalcon\Storage\SerializerFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\CacheFactory;
use Phalcon\Di\DiInterface;

class CacheTools
{
	private $container;

	protected $config;

	protected $cacheTimeout;

	protected $cacheService;

	protected $cache;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->config = $this->container->getShared('config');

		if ($this->config->cacheTimeout && $this->config->cacheService) {
			$this->cacheTimeout = $this->config->cacheTimeout;
			$this->cacheService = $this->config->cacheService;
		} else {
			$this->cacheTimeout = 3600;// Default seconds
			$this->cacheService = 'streamCache';
		}

		$this->cache = $this->container->getShared($this->cacheService);
	}

	public function addModelCacheParameters($parameters = null, $cacheName = null) {

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

	public function get($cacheKey)
	{
		if ($this->cache->has($cacheKey)) {
			return $this->cache->get($cacheKey);
		}

		return null;
	}
}