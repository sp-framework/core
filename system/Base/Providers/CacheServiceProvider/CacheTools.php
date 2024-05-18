<?php

namespace System\Base\Providers\CacheServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;

class CacheTools
{
	protected $cacheTimeout;

	protected $cacheService;

	protected $cache;

	protected $cacheConfig;

	protected $localContent;

	protected $opCache;

	protected $index;

	public $caches;

	public function __construct($cacheConfig, array $caches, $localContent, $opCache)
	{
		$this->cacheConfig = $cacheConfig;

		if ($this->cacheConfig->timeout && $this->cacheConfig->service) {
			$this->cacheTimeout = $this->cacheConfig->timeout;
			$this->cacheService = $this->cacheConfig->service;
		} else {
			$this->cacheTimeout = 3600;// Default seconds
			$this->cacheService = 'streamCache';
		}

		$this->caches = $caches;

		$this->cache = $caches[$this->cacheService];

		$this->localContent = $localContent;

		$this->opCache = $opCache;
	}

	public function getAvailableCaches()
	{
		return array_keys($this->caches);
	}

	public function addModelCacheParameters($parameters = null, $cacheName = null)
	{
		if ($parameters && $cacheName) {
			$cacheKey[0] = $cacheName;
			$cacheKey[] = $parameters;
		} else if (!$parameters && $cacheName) {
			$cacheKey[0] = $cacheName;
		} else if ($parameters && !$cacheName) {
			$cacheKey = $parameters;
		}

		$key = $this->generateCacheKey($cacheKey);

		if ($cacheName) {
			$this->createIndexFile($cacheName);
		} else {
			$this->createIndexFile($cacheKey);
		}

		if (!isset($parameters['cache'])) {
			$parameters['cache'] = [
				'key'      	=> $key,
				'lifetime' 	=> $this->cacheTimeout,
				'service' 	=> $this->cacheService,
			];
		}

		return $parameters;
	}

	public function generateCacheKey($cacheKey)
	{
		$cacheKey = $this->helper->encode($cacheKey);

		$cacheKey = preg_replace('/[^A-Za-z0-9.-]/', '', $cacheKey);

		$key = md5($cacheKey);

		return $key;
	}

	public function deleteCache($cacheKey)
	{
		if ($this->cache && $this->cache->has($cacheKey)) {
			$this->cache->delete($cacheKey);

			return true;
		}

		return false;
	}

	public function getCache($cacheKey)
	{
		if ($this->cacheConfig->enabled) {
			if ($this->cache->has($cacheKey)) {
				return $this->cache->get($cacheKey);
			}
		}

		return false;
	}

	public function setCache(string $cacheKey, $data)
	{
		if ($this->cacheConfig->enabled) {
			$this->cache->set($cacheKey, $data);

			return true;
		}

		return false;
	}

	public function createIndexFile($cacheName, $recreate = false)
	{
		$content['all'] = [];
		$content['list'] = [];
		$content['ids'] = [];

		if ($this->opCache) {
			if (!$this->opCache->getCache($cacheName, 'index')) {
				$this->opCache->setCache($cacheName, $content, 'index');
			} else if ($recreate) {
				$this->opCache->resetCache($cacheName, $content, 'index');
			}
		} else {
			if (!$this->localContent->fileExists('var/storage/cache/stream/index/' . $cacheName . '.json') ||
				$recreate
			) {
				$this->localContent->write('var/storage/cache/stream/index/' . $cacheName . '.json', $this->helper->encode($content));
			}
		}
	}

	public function updateIndex($cacheName, $parameters, $object, $list = false, $id = false)
	{
		if (!isset($parameters['cache']) && !isset($parameters['cache']['key'])) {
			return;
		}

		$index = $this->getIndex($cacheName);

		if ($index) {
			if ($list && $object) {
				if (!isset($index['all'][$parameters['cache']['key']]) ||
					!isset($index['list'][$parameters['cache']['key']])
				) {
					$index['all'][$parameters['cache']['key']] = '1';

					$index['list'][$parameters['cache']['key']] = $parameters;

					if ($object->count() > 0) {
						foreach ($object as $obj) {
							if (!isset($index['ids'][$obj->id][$parameters['cache']['key']])) {
								$index['ids'][$obj->id][$parameters['cache']['key']] = 'list';
							}
						}
					}
				} else {
					return;
				}
			} else if ($id && $object) {
				if (!isset($index['ids'][$object->id][$parameters['cache']['key']])) {
					$index['ids'][$object->id][$parameters['cache']['key']] = $parameters;
				} else {
					return;
				}
			}

			if ($this->opCache) {
				$this->opCache->resetCache($cacheName, $index, 'index');
			} else {
				try {
					$this->localContent->write('var/storage/cache/stream/index/' . $cacheName . '.json', $this->helper->encode($index));
				} catch (FilesystemException | UnableToWriteFile | \Exception $e) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	public function resetCache($cacheName = null, $id = null, $removeId = false)
	{
		$this->index = null;

		if (!$cacheName) {//Only do this in maintenance mode
			$this->cache->clear();

			if ($this->opCache) {
				$this->opCache->resetCache(null, 'index');
			} else {
				$this->localContent->deleteDirectory('var/storage/cache/stream/');
			}

			$this->index = null;

			return;
		}

		if ($cacheName && $id) {
			$keys = $this->getKeysFromIndex($cacheName, $id);
		} else {
			$keys = $this->getKeysFromIndex($cacheName);
		}

		if ($keys && is_array($keys) && count($keys) > 0) {
			foreach ($keys as $key => $parameters) {
				$this->deleteCache($key);

				if ($cacheName && $id) {
					$this->removeKeyFromIndex($cacheName, $key, $id, $removeId);
				} else {
					$this->removeKeyFromIndex($cacheName, $key);
				}
			}

			if ($this->opCache) {
				$this->opCache->resetCache($cacheName, $this->index, 'index');
			} else {
				$this->localContent->write('var/storage/cache/stream/index/' . $cacheName . '.json', $this->helper->encode($this->index));
			}
		}

		$this->index = null;
	}

	protected function getKeysFromIndex($cacheName, $id = null)
	{
		if (!$this->index) {
			$this->index = $this->getIndex($cacheName);
		}

		if ($this->index) {
			if ($id) {
				if (isset($this->index['ids'][$id])) {
					return $this->index['ids'][$id];
				}
			} else {
				if (isset($this->index['list'])) {
					return $this->index['list'];
				}
			}

			return false;
		}
	}

	protected function removeKeyFromIndex($cacheName, $key = null, $id = null, $removeId = false)
	{
		if (!$this->index) {
			$this->getIndex($cacheName);
		}

		if ($key) {
			if (isset($this->index['all'][$key])) {
				unset($this->index['all'][$key]);
			}
			if (isset($this->index['list'][$key])) {
				unset($this->index['list'][$key]);
			}
		}

		if ($id && $removeId) {
			if (isset($this->index['ids'][$id])) {
				unset($this->index['ids'][$id]);
			}
		}
	}

	protected function getIndex($cacheName)
	{
		if ($this->opCache) {
			$index = $this->opCache->getCache($cacheName, 'index');

			$this->index = $index;

			return $index;
		} else {
			try {
				$index = $this->localContent->read('var/storage/cache/stream/index/' . $cacheName . '.json');

				$index = $this->helper->decode($index, true);

				$this->index = $index;

				return $index;
			} catch (FilesystemException | UnableToReadFile | \Exception $e) {
				if (str_contains($e->getMessage(), "json_decode") ||
					get_class($e) !== 'FilesystemException' ||
					get_class($e) !== 'UnableToReadFile'
				) {
					$this->createIndexFile($cacheName, true);

					return $this->getIndex($cacheName);
				} else {
					throw $e;
				}
			}
		}
	}
}