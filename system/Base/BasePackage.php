<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Controller;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

abstract class BasePackage extends Controller
{
	public $packagesData;

	protected $cacheKey;

	protected $cacheKeys = [];

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;

		if (!$this->cacheKey) {
			$this->resetCacheKey();
		}
	}

	protected function getIdParams(int $id)
	{
		return
			[
				'conditions'	=> 'id = :id:',
				'bind'			=>
					[
						'id'	=> $id
					]
			];
	}

	protected function extractCacheKey()
	{
		$reflection = new \ReflectionClass($this);

		$class = explode('\\', $reflection->getName());

		$key = [];

		foreach ($class as $value) {
			array_push($key, substr($value, 0, 3));
		}

		return strtolower(join($key));
	}

	public function resetCacheKey()
	{
		$this->cacheKeys = [];

		$this->setCacheKey($this->extractCacheKey());
	}

	public function setCacheKey($key)
	{
		$this->cacheKey = $key;

		$this->cacheKeys[0] = $this->cacheKey;
	}

	public function getCacheKey()
	{
		return $this->cacheKey;
	}

	protected function usePackage($packageClass)
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if ($this->checkPackage($packageClass)) {
			return new $packageClass($this->container);
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		$packageName = Arr::last(explode('\\', $packageClass));

		$packageApplicationId =
			$this->packages[array_search($packageName, array_column($this->packages, 'name'))]['application_id'];

		if ($packageApplicationId === $this->application['id']) {
			return true;
		} else {
			return false;
		}
	}

	public function getPackagesData()
	{
		return $this->packagesData->getAllData();
	}

	protected function paramsWithCache(array $parameters)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters($parameters, $this->getCacheKey());
		}

		$this->cacheKey = $parameters['cache']['key'];

		return $parameters;
	}

	//Very broad at the moment, we need to narrow down search and delete caching
	protected function resetCaches(int $id = null)
	{
		foreach ($this->cacheKeys as $key => $cacheKey) {
			$cache = $this->cacheTools->get($cacheKey);
			if ($cache) {
				if ($id) {
					$cache->filter(
						function ($search) use ($id, $cacheKey) {
							if ($search->id == $id) {
								$this->cacheTools->deleteCache($cacheKey);
							}
						}
					);
				} else {
					$this->cacheTools->deleteCache($cacheKey);
				}
			}
		}
	}

	protected function resetCache(int $id = null)
	{
		$this->resetCacheKey();

		if ($id) {
			array_push(
				$this->cacheKeys,
				$this->paramsWithCache(
					$this->getIdParams($id)
				)['cache']['key']
			);
		}

		$this->resetCaches($id);
	}

	protected function updateCache(int $id)
	{
		$this->resetCache($id);

		$this->resetCacheKey();

		$this->get($id);//Generate new Cache
	}
}