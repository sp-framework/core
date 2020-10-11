<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Controller;
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

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;

		$this->setDefaultPackageResponse();

		if (!$this->cacheKey) {
			$this->resetCacheKey();
		}

		$this->setNames();
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

		if (!$this->{$this->packageName} || $resetCache) {

			$this->model = $this->modelToUse::find($parameters);

			$this->{$this->packageName} = $this->model->toArray();
		}

		return $this;
	}

	public function getById(int $id, bool $resetCache = false, bool $enableCache = true)
	{
		if ($id) {
			if ($enableCache) {
				$parameters = $this->paramsWithCache($this->getIdParams($id));
			} else {
				$parameters = [];
			}

			$this->model = $this->modelToUse::find($parameters);

			return $this->getDbData($parameters, $enableCache);
		}

		throw new \Exception('getById needs id parameter to be set.');
	}

	public function getByParams(array $params, bool $resetCache = false, bool $enableCache = true)
	{
		if ($params && $params['conditions']) {
			if ($enableCache) {
				$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
			} else {
				$parameters = $params;
			}

			$this->model = $this->modelToUse::find($parameters);

			return $this->getDbData($parameters, $enableCache);
		}

		throw new \Exception('getByParams needs parameter condition to be set.');
	}

	public function getDbData($parameters, bool $enableCache = true)
	{
		if ($this->model->count() === 1) {
			$this->packagesData->responseCode = 0;
			$this->packagesData->responseMessage = 'Found';

			if ($enableCache) {
				array_push($this->cacheKeys, $parameters['cache']['key']);
			}

			return $this->model->toArray()[0];

		} else if ($this->model->count() > 1) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';

		} else if ($this->model->count() === 0) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'No Record Found!';
		}

		$this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

		return false;
	}

	public function add(array $data)
	{
		if ($data) {
			try {
				$txManager = new Manager();
				$transaction = $txManager->get();

				${$this->packageName} = new $this->modelToUse();

				${$this->packageName}->setTransaction($transaction);

				${$this->packageName}->assign($data);

				$create = ${$this->packageName}->create();

				if (!$create) {
					$transaction->rollback("Could not add {$this->packageNameS}.");
				}

				if ($transaction->commit()) {
					$this->resetCache();

					$this->packagesData->responseCode = 0;

					$this->packagesData->responseMessage = "Added {$this->packageNameS}!";

					return true;
				}
			} catch (\Exception $e) {
				throw $e;
			}
		}

		throw new \Exception('Data array missing. Cannot add!');
	}

	public function update(array $data)
	{
		if ($data) {
			try {
				$txManager = new Manager();
				$transaction = $txManager->get();

				${$this->packageName} = new $this->modelToUse();

				${$this->packageName}->setTransaction($transaction);

				${$this->packageName}->assign($data);

				if (!${$this->packageName}->update()) {
					$transaction->rollback("Could not update {$this->packageNameS}.");
				}

				if ($transaction->commit()) {
					//Delete Old cache if exists and generate new cache
					$this->updateCache($data['id']);

					$this->packagesData->responseCode = 0;

					$this->packagesData->responseMessage = "{$this->packageNameS} Updated!";

					return true;
				}
			} catch (\Exception $e) {
				throw $e;
			}
		}

		throw new \Exception('Data array missing. Cannot update!');
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
		$this->packagesData->responseCode = '0';

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
	}

	// protected function regenerateCaches(int $id = null)
	// {
	// 	if ($id) {
	// 		$this->get($id);
	// 	}
	// }
}