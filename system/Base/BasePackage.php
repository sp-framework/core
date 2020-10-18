<?php

namespace System\Base;

use Phalcon\Helper\Arr;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Failed;
use Phalcon\Mvc\Model\Transaction\Manager;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

abstract class BasePackage extends Controller implements BasePackageInterface
{
	public $packagesData;

	protected $packageName;

	protected $packageNameP;

	protected $packageNameS;

	protected $model;

	protected $cacheKey;

	protected $cacheKeys = [];

	protected $manager = null;

	protected $transaction = null;

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;

		$this->setDefaultPackageResponse();

		if (!$this->cacheKey) {
			$this->resetCacheKey();
		}

		$this->setNames();
	}

	public function init()
	{
		return $this;
	}

	protected function initTransaction()
	{
		if (!$this->manager) {
			$this->manager = new Manager();
		}

		if (!$this->transaction) {
			$this->transaction = $this->manager->get();
		}
	}

	protected function setNames()
	{
		if (!$this->packageName) {
			$this->packageName = strtolower(Arr::last($this->getClassName()));
		}

		if (!$this->packageNameP) {
			$this->packageNameP = ucfirst($this->packageName);
		}

		if (!$this->packageNameS) {
			$this->packageNameS = ucfirst(rtrim($this->packageName, 's'));
		}
	}

	public function getAll(bool $resetCache = false, bool $enableCache = true)
	{
		if ($enableCache) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		} else {
			$parameters = [];
		}

		if (!$this->config->cache->enabled) {
			$parameters = [];
		}

		if (!$this->{$this->packageName} || $resetCache) {

			$this->model = $this->modelToUse::find($parameters);

			$this->{$this->packageName} = $this->model->toArray();
		}
	}

	public function getById(int $id, bool $resetCache = false, bool $enableCache = true)
	{
		if ($id) {
			if ($enableCache) {
				$parameters = $this->paramsWithCache($this->getIdParams($id));
			} else {
				$parameters = $this->getIdParams($id);
			}

			if (!$this->config->cache->enabled) {
				$parameters = $this->getIdParams($id);
			}

			$this->model = $this->modelToUse::find($parameters);

			return $this->getDbData($parameters, $enableCache);
		} else {
			throw new \Exception('getById needs id parameter to be set.');
		}
	}

	public function getByParams(array $params, bool $resetCache = false, bool $enableCache = true)
	{
		if ($params && $params['conditions']) {
			if ($enableCache) {
				$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
			} else {
				$parameters = $params;
			}

			if (!$this->config->cache->enabled) {
				$parameters = $params;
			}

			$this->model = $this->modelToUse::find($parameters);

			return $this->getDbData($parameters, $enableCache, 'params');
		}

		throw new \Exception('getByParams needs parameter condition to be set.');
	}

	public function getDbData($parameters, bool $enableCache = true, string $type = 'id')
	{
		if ($this->model->count() === 0) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'No Record Found!';

			return;
		}

		if ($type === 'id') {
			if ($this->model->count() === 1) {
				$this->packagesData->responseCode = 0;
				$this->packagesData->responseMessage = 'Found';

				if ($enableCache && $this->config->cache->enabled) {
					array_push($this->cacheKeys, $parameters['cache']['key']);
				}

				return $this->model->toArray()[0];

			} else if ($this->model->count() > 1) {
				$this->packagesData->responseCode = 1;
				$this->packagesData->responseMessage =
					'Duplicate Id found! Database Corrupt'; //Run package to fix database

				return;
			}
		} else if ($type === 'params') {
			if ($enableCache) {
				array_push($this->cacheKeys, $parameters['cache']['key']);
			}

			return $this->model->toArray();
		}

		$this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

		return false;
	}

	public function add(array $data)
	{
		if ($data) {
			${$this->packageName} = new $this->modelToUse();

			${$this->packageName}->assign($data);

			$create = ${$this->packageName}->create();

			if (!$create) {
				$errMessages = [];

				foreach (${$this->packageName}->getMessages() as $value) {
					array_push($errMessages, $value->getMessage());
				}

				throw new \Exception(
					"Could not update {$this->packageNameS}. Reasons: <br>" .
					join(',', $errMessages)
				);
			} else {
				$this->resetCache();

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = "Added {$this->packageNameS}!";

				$this->packagesData->last = ${$this->packageName};

				return true;
			}
		} else {
			throw new \Exception('Data array missing. Cannot add!');
		}
	}

	public function update(array $data)
	{
		if ($data) {
			${$this->packageName} = $this->modelToUse::findFirstById($data['id']);

			${$this->packageName}->assign($data);

			$update = ${$this->packageName}->update();

			if (!$update) {
				$errMessages = [];

				foreach (${$this->packageName}->getMessages() as $value) {
					array_push($errMessages, $value->getMessage());
				}

				throw new \Exception(
					"Could not update {$this->packageNameS}. Reasons: <br>" .
					join(',', $errMessages)
				);
			} else {
				//Delete Old cache if exists and generate new cache
				$this->updateCache($data['id']);

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = "{$this->packageNameS} Updated!";

				$this->packagesData->last = ${$this->packageName};

				return true;
			}
		} else {
			throw new \Exception('Data array missing. Cannot update!');
		}
	}

	public function remove(int $id)
	{
		if ($this->packageName === 'core') {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = "Could not delete {$this->packageNameS}.";
			return;
		}

		//Need to solve dependencies for removal
		$this->getById($id);

		if ($this->model->count() === 1) {
			if ($this->model->delete()) {

				$this->resetCache($id);

				$this->packagesData->responseCode = 0;
				$this->packagesData->responseMessage = "{$this->packageNameS} Deleted!";
				return true;
			} else {
				$this->packagesData->responseCode = 1;
				$this->packagesData->responseMessage = "Could not delete {$this->packageNameS}.";
			}
		} else if ($this->model->count() > 1) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
		} else if ($this->model->count() === 0) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'No Record Found with that ID!';
		}
	}

	protected function setDefaultPackageResponse()
	{
		$this->packagesData->responseCode = 0;

		$this->packagesData->responseMessage = 'Default Response Message';
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

	protected function getClassName()
	{
		$reflection = new \ReflectionClass($this);

		return explode('\\', $reflection->getName());
	}

	protected function extractCacheKey()
	{
		$class = $this->getClassName();

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
		if (!$this->config->cache->enabled) {
			return;
		}

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
		if (!$this->config->cache->enabled) {
			return;
		}
		$this->resetCache($id);

		$this->resetCacheKey();
	}

	// protected function regenerateCaches(int $id = null)
	// {
	// 	if ($id) {
	// 		$this->get($id);
	// 	}
	// }
}