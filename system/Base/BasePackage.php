<?php

namespace System\Base;

use Phalcon\Helper\Arr;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Paginator\Adapter\Model;
use Phalcon\Paginator\Adapter\NativeArray;
use Phalcon\Paginator\Exception;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

abstract class BasePackage extends Controller
{
	public $packagesData;

	protected $application;

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
		$this->application = $this->modules->applications->getApplicationInfo();

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
		}

		throw new \Exception('getById needs id parameter to be set.');
	}

	public function getAll(bool $resetCache = false, bool $enableCache = true)
	{
		if ($enableCache && $this->config->cache->enabled) {
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

	public function getByParams(array $params, bool $resetCache = false, bool $enableCache = true)
	{
		if (isset($params['conditions'])) {
			if ($enableCache && $this->config->cache->enabled) {
				$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
			} else {
				$parameters = $params;
			}

			$this->model = $this->modelToUse::find($parameters);

			return $this->getDbData($parameters, $enableCache, 'params');
		}

		throw new \Exception('getByParams needs parameter condition to be set.');
	}

	public function getPaged(array $params = [], bool $resetCache = false, bool $enableCache = true)
	{
		//Empty columns causes SQL error :APL0:
		if (isset($params['columns']) && count($params['columns']) === 0) {
			unset($params['columns']);
		}

		$pageParams['currentPage'] =
			isset($this->request->getPost()['page']) ?
			$this->request->getPost()['page'] :
			1;

		$pageParams['limit'] =
			isset($this->request->getPost()['limit']) ?
			$this->request->getPost()['limit'] :
			20;

		if ($pageParams['currentPage'] > 1) {
			$offset = ['offset' => $pageParams['currentPage'] * $pageParams['limit']];
		}


		$params =
			array_merge(
				$params,
				[
					'limit'			=> $pageParams['limit'],
					'offset'		=>
						$pageParams['currentPage'] > 1 ?
						($pageParams['currentPage'] - 1) * $pageParams['limit'] :
						0,
				]
			);

		if (isset($this->request->getPost()['conditions'])) {
			$postConditions = explode('&', $this->request->getPost()['conditions']);

			$conditions = '';
			$bind = [];

			foreach ($postConditions as $conditionKey => $condition) {
				$conditionArr = explode(':', $condition);

				if ($conditionArr[1] === 'equals') {
					$sign = '=';
				} else if ($conditionArr[1] === 'between') {
					$sign = 'BETWEEN';
				} else if ($conditionArr[1] === 'notequals') {
					$sign = '<>';
				} else if ($conditionArr[1] === 'notbetween') {
					$sign = 'NOT BETWEEN';
				}
				// var_dump($conditionArr);

				if ($sign === 'BETWEEN') {
					$valueArr = explode(',', $conditionArr[2]);

					$conditions .=
						'(' . $conditionArr[0] . ' ' . $sign;

					foreach ($valueArr as $valueKey => $valueValue) {
						$conditions .=
							' :baz_' . $conditionKey . '_' . $valueKey . '_' . $conditionArr[0] . ':';

						$bind[
							'baz_' . $conditionKey . '_' . $valueKey . '_' . $conditionArr[0]
						] = $valueValue;

						if (Arr::lastKey($valueArr) !== $valueKey) {
							$conditions .= ' AND';
						}
					}

					$conditions .=
						')';
				} else {
					$valueArr = explode(',', $conditionArr[2]);

					if (count($valueArr) > 1) {
						foreach ($valueArr as $valueKey => $valueValue) {
							$conditions .=
								$conditionArr[0] . ' ' . $sign .
								' :baz_' . $conditionKey . '_' . $valueKey . '_' . $conditionArr[0] . ':';

							$bind[
								'baz_' . $conditionKey . '_' . $valueKey . '_' . $conditionArr[0]
							] = $valueValue;

							if (Arr::lastKey($valueArr) !== $valueKey) {
								$conditions .= ' OR ';
							}
						}
					} else {
						$conditions .=
							$conditionArr[0] . ' ' . $sign . ' :baz_' . $conditionKey . '_' . $conditionArr[0] . ':';

						$bind[
							'baz_' . $conditionKey . '_' . $conditionArr[0]
						] = $valueArr[0];
					}

				}
				if (Arr::lastKey($postConditions) !== $conditionKey) {
					$conditions .= ' ' . strtoupper($conditionArr[3]) . ' ';
				}
			}


			var_dump($conditions, $bind);
			// var_dump($this->request->getPost()['conditions']);
			$params =
				array_merge(
					$params,
					[
						'conditions'	=> $conditions,
						'bind'			=> $bind
					]
				);

		} else {
			$params =
				array_merge(
					$params,
					[
						'conditions'	=> '',
					]
				);
		}


		// var_dump($this->modelToUse::count());
			// var_dump($params);
		$data = $this->getByParams($params);

		if ($data) {
			$pageParams['data'] = $data;
		} else {
			$pageParams['data'] = [];
		}
			// var_dump($pageParams);

		// if ($this->modelToUse) {
		// 	$pageParams['model'] = $this->modelToUse;
		// } else {
		// 	throw new \Exception('getPaged need modelToUse property, which is not set in package.');
		// }

		// if ($enableCache && $this->config->cache->enabled) {
		// 	$pageParams['parameters'] = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
		// } else {
		// 	$pageParams['parameters'] = $params;
		// }
		// var_dump($pageParams);
		try {
			$paginator = new NativeArray($pageParams);
		// var_dump($paginator);

			$paged = $paginator->paginate();

			// var_dump($paged);
		// var_dump($paged);

			$paginationCounters['total_items'] = $this->modelToUse::count();
			$paginationCounters['limit'] = (int) $pageParams['limit'];
			$paginationCounters['first'] = 1;
			$paginationCounters['previous'] = (int) $pageParams['currentPage'] > 1 ? $pageParams['currentPage'] - 1 : 1;
			$paginationCounters['current'] = (int) $pageParams['currentPage'];
			$paginationCounters['next'] = $pageParams['currentPage'] + 1;
			$paginationCounters['last'] = (int) ceil($paginationCounters['total_items'] / ($paginationCounters['limit']));

			$this->packagesData->paginationCounters = $paginationCounters;

			return $paged;
		} catch (Exception $e) {
			throw $e;
		}
	}

	protected function getDbData($parameters, bool $enableCache = true, string $type = 'id', bool $returnArray = true)
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
			if ($enableCache && $this->config->cache->enabled) {
				array_push($this->cacheKeys, $parameters['cache']['key']);
			}

			if (!$returnArray) {
				$this->model;
			} else {
				return $this->model->toArray();
			}
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
		return
			$this->modules->packages->getNamedPackageForApplication(
				Arr::last(explode('\\', $packageClass)),
				$this->application['id']
			);
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
			$cache = $this->cacheTools->getCache($cacheKey);
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

	public function getModel()
	{
		return $this->model;
	}

	public function getModelToUse()
	{
		return $this->modelToUse;
	}

	protected function getModelsMetaData()
	{
		if ($this->modelToUse) {
			$model = new $this->modelToUse();

			return $model->getModelsMetaData();
		}

		return false;

	}

	public function getModelsColumnMap(array $filter = [])
	{
		$metadata = [];

		$md = $this->getModelsMetaData();

		if ($md) {
			$dataTypes = $md->getDataTypes(new $this->modelToUse());
			$numeric = $md->getDataTypesNumeric(new $this->modelToUse());
			$columns = $md->getAttributes(new $this->modelToUse());

			$filteredColumns = [];

			if (count($filter) > 0) {
				foreach ($columns as $column) {
					if (in_array($column, $filter)) {
						array_push($filteredColumns, $column);
					}
				}
			} else {
				$filteredColumns = $columns;
			}

			foreach ($filteredColumns as $filteredColumn) {
				$metadata[$filteredColumn]['id'] = $filteredColumn;
				$metadata[$filteredColumn]['name'] = str_replace('_', ' ', $filteredColumn);

				if (isset($numeric[$filteredColumn]) && $numeric[$filteredColumn] === true) {
					$metadata[$filteredColumn]['data']['numeric'] = 'true';
				} else {
					$metadata[$filteredColumn]['data']['numeric'] = 'false';
				}

				$metadata[$filteredColumn]['data']['dataType'] = $dataTypes[$filteredColumn];
			}

			return $metadata;
		}

		return false;
	}
	// protected function regenerateCaches(int $id = null)
	// {
	// 	if ($id) {
	// 		$this->get($id);
	// 	}
	// }
	public function describe(string $table = null, $indexes = false, $references = false)
	{
		if (!$table) {
			return $this->db->listTables($this->config->db->dbname);
		} else {
			if ($indexes) {
				return $this->db->describeIndexes($table);
			}
			if ($references) {
				return $this->db->describeReferences($table);
			}
			return $this->db->describeColumns($table);
		}
	}

	public function dbViews()
	{
		return $this->db->listViews($this->config->db->dbname);
	}

	public function dbViewExists(string $view)
	{
		return $this->db->viewExists($view);
	}

	public function tableExists(string $table)
	{
		return $this->db->tableExists($table);
	}

	public function createTable(string $table, array $columns, $drop = false)
	{
		if ($drop) {
			$this->dropTable($table);
		}

		return $this->db->createTable($table, '', $columns);
	}

	public function alterTable()
	{
		//
	}

	public function dropTable(string $table)
	{
		$this->db->dropTable($table);
	}
}