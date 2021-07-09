<?php

namespace System\Base;

use League\Flysystem\StorageAttributes;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Paginator\Adapter\Model;
use Phalcon\Paginator\Adapter\NativeArray;
use Phalcon\Paginator\Exception;
use System\Base\Exceptions\IdNotFoundException;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ActivityLogs;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

abstract class BasePackage extends Controller
{
	public $packagesData;

	protected $app;

	protected $domain;

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
		$this->app = $this->apps->getAppInfo();

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

			$data = $this->getDbData($parameters, $enableCache);

			if ($data) {
				return $data;
			} else {
				// return false;
				throw new IdNotFoundException('Not Found', 1);
			}
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

			$data = $this->getDbData($parameters, $enableCache, 'params');

			if ($data) {
				return $data;
			} else {
				return false;
				// throw new IdNotFoundException('Not Found', 1);
			}
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

		if (isset($this->request->getPost()['conditions']) &&
			$this->request->getPost()['conditions'] !== ''
		) {
			$postConditions = explode('&', rtrim($this->request->getPost()['conditions'], '&'));
			$conditions = '';
			$bind = [];

			foreach ($postConditions as $conditionKey => $condition) {
				$conditionArr = explode(':', $condition);

				if (Arr::firstKey($postConditions) !== $conditionKey) {
					if ($conditionArr[0] === '') {
						$conditions .= ' AND ';//Default for AND/OR
					} else {
						$conditions .= ' ' . strtoupper($conditionArr[0]) . ' ';
					}
				}

				$conditionArr[1] = str_replace(' ', '_', strtolower($conditionArr[1]));

				if ($conditionArr[2] === 'equals') {
					$sign = '=';
				} else if ($conditionArr[2] === 'notequals') {
					$sign = '<>';
				} else if ($conditionArr[2] === 'greaterthan') {
					$sign = '>';
				} else if ($conditionArr[2] === 'greaterthanequals') {
					$sign = '>=';
				} else if ($conditionArr[2] === 'lessthan') {
					$sign = '<';
				} else if ($conditionArr[2] === 'lessthanequals') {
					$sign = '<=';
				} else if ($conditionArr[2] === 'like') {
					$sign = 'LIKE';
				} else if ($conditionArr[2] === 'notlike') {
					$sign = 'NOT LIKE';
				} else if ($conditionArr[2] === 'between') {
					$sign = 'BETWEEN';
				} else if ($conditionArr[2] === 'notbetween') {
					$sign = 'NOT BETWEEN';
				} else if ($conditionArr[2] === 'empty') {
					$sign = 'IS NULL';
				} else if ($conditionArr[2] === 'notempty') {
					$sign = 'IS NOT NULL';
				}

				if ($conditionArr[2] === 'between' || $conditionArr[2] === 'notbetween') {
					$valueArr = explode(',', $conditionArr[3]);

					if ($conditionArr[2] === 'between') {
						$conditions .=
							$conditionArr[1] . ' ' . $sign;
					} else if ($conditionArr[2] === 'notbetween') {
						$conditions .=
						'NOT ' . $conditionArr[1] . ' BETWEEN';
					}

					foreach ($valueArr as $valueKey => $valueValue) {
						$conditions .=
							' :baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

						$bind[
							'baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
						] = $valueValue;

						if (Arr::lastKey($valueArr) !== $valueKey) {
							$conditions .= ' AND';
						}
					}
				} else if ($conditionArr[2] === 'empty' || $conditionArr[2] === 'notempty') {
					$conditions .= $conditionArr[1] . ' ' . $sign;
				} else {
					$valueArr = explode(',', $conditionArr[3]);

					if (count($valueArr) > 1) {
						foreach ($valueArr as $valueKey => $valueValue) {
							$conditions .=
								$conditionArr[1] . ' ' . $sign .
								' :baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

							$bind[
								'baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
							] = $valueValue;

							if (Arr::lastKey($valueArr) !== $valueKey) {
								$conditions .= ' OR ';
							}
						}
					} else {

						$conditions .=
							$conditionArr[1] . ' ' . $sign . ' :baz_' . $conditionKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

						$bind[
							'baz_' . $conditionKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
						] = $valueArr[0];
					}
				}
			}

			$filterConditions =
				[
					'conditions'	=> $conditions,
					'bind'			=> $bind
				];

			$params = array_merge($params, $filterConditions);

		} else {
			$params =
				array_merge(
					$params,
					[
						'conditions'	=> '',
					]
				);
		}

		if (isset($this->request->getPost()['order']) &&
			$this->request->getPost()['order'] !== ''
		) {
			$params =
				array_merge(
					$params,
					[
						'order'	=> $this->request->getPost()['order']
					]
				);
		}

		$data = $this->getByParams($params);

		if ($data) {
			$pageParams['data'] = $data;
		} else {
			$pageParams['data'] = [];
		}

		try {
			$paginator = new NativeArray($pageParams);

			$paged = $paginator->paginate();

			$paginationCounters['total_items'] = $this->modelToUse::count();

			if (isset($filterConditions)) {
				$paginationCounters['filtered_items'] = $this->modelToUse::count($filterConditions);
			} else {
				$paginationCounters['filtered_items'] = $paginationCounters['total_items'];
			}

			$paginationCounters['limit'] = (int) $pageParams['limit'];
			$paginationCounters['first'] = 1;
			$paginationCounters['previous'] =
				(int) $pageParams['currentPage'] > 1 ? $pageParams['currentPage'] - 1 : 1;
			$paginationCounters['current'] =
				(int) $pageParams['currentPage'];
			$paginationCounters['next'] = $pageParams['currentPage'] + 1;
			$paginationCounters['last'] =
				(int) ceil($paginationCounters['filtered_items'] / ($paginationCounters['limit']));

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
				return $this->model;
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
			$data = $this->jsonData($data);

			${$this->packageName} = new $this->modelToUse();

			${$this->packageName}->assign($data);

			$create = ${$this->packageName}->create();

			if ($create) {
				$this->resetCache();

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = "Added {$this->packageNameS}!";

				$this->packagesData->last = ${$this->packageName}->toArray();

				return true;
			} else {
				$errMessages = [];

				foreach (${$this->packageName}->getMessages() as $value) {
					array_push($errMessages, $value->getMessage());
				}

				throw new \Exception(
					"Could not add {$this->packageNameS}. Reasons: <br>" .
					join(',', $errMessages)
				);
			}
		} else {
			throw new \Exception('Data array missing. Cannot add!');
		}
	}

	public function update(array $data)
	{
		if ($data) {

			$data = $this->jsonData($data);

			${$this->packageName} = $this->modelToUse::findFirstById($data['id']);

			if (!${$this->packageName}) {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'ID: ' . $data['id'] . " not found for package {$this->packageName}";

				return;
			}

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

				$this->packagesData->last = ${$this->packageName}->toArray();

				return true;
			}
		} else {
			throw new \Exception('Data array missing. Cannot update!');
		}
	}

	protected function jsonData(array $data)
	{
		foreach ($data as $dataKey => $dataValue) {
			if (is_array($dataValue)) {
				$data[$dataKey] = Json::encode($dataValue);
			}
		}

		return $data;
	}

	public function remove(int $id)
	{
		//Move this to Modules Package
		// if ($this->packageName === 'core') {
		// 	$this->packagesData->responseCode = 1;
		// 	$this->packagesData->responseMessage = "Could not delete {$this->packageNameS}.";
		// 	return;
		// }
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

	public function clone(int $id, string $addCloneTxtToColumn = 'name', array $data = null)
	{
		if (!$data) {
			$source = $this->getById($id);
		} else {
			$source = $data;
		}

		if ($source) {

			unset($source['id']);

			if (isset($source[$addCloneTxtToColumn])) {

				$orgSource = $source[$addCloneTxtToColumn];

				$source[$addCloneTxtToColumn] = $source[$addCloneTxtToColumn] . ' (Clone)';

			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Clone Text cannot be added to column ' . $addCloneTxtToColumn . '. Please provide proper column data';

				return false;
			}

			$add = $this->add($source);

			if ($add) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = $orgSource . ' cloned successfully';

				return true;
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

		$this->packagesData->responseMessage = 'OK';
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
			// array_push($key, substr($value, 0, 4));
			array_push($key, $value);
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
		$this->app = $this->apps->getAppInfo();

		if ($this->checkPackage($packageClass)) {
			return (new $packageClass())->init();
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for app ' . $this->app['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getNamedPackageForApp(
				Arr::last(explode('\\', $packageClass)),
				$this->app['id']
			);
	}

	public function getPackagesData()
	{
		return $this->packagesData->getAllData();
	}

	protected function paramsWithCache(array $params)
	{
		if ($this->cacheKey) {
			$params = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
		}

		$this->cacheKey = $params['cache']['key'];

		return $params;
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
			$number = $md->getDataTypesNumeric(new $this->modelToUse());
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

				if (isset($number[$filteredColumn]) && $number[$filteredColumn] === true) {
					$metadata[$filteredColumn]['data']['number'] = 'true';
				} else {
					$metadata[$filteredColumn]['data']['number'] = 'false';
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

	public function createTable(string $table, string $dbName = '', array $columns, $drop = false)
	{
		try {
			if ($drop) {
				$this->dropTable($table);
			}

			return $this->db->createTable($table, $dbName, $columns);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function alterTable()
	{
		//
	}

	public function dropTable(string $table)
	{
		$this->db->dropTable($table);
	}

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$installedFiles['files'] =
				$this->localContent->listContents($directory, $sub)
				->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
				->map(fn (StorageAttributes $attributes) => $attributes->path())
				->toArray();

			$installedFiles['dirs'] =
				$this->localContent->listContents($directory, $sub)
				->filter(fn (StorageAttributes $attributes) => $attributes->isDir())
				->map(fn (StorageAttributes $attributes) => $attributes->path())
				->toArray();

			return $installedFiles;
		} else {
			return null;
		}
	}

	protected function addActivityLog(array $data, $oldData = null)
	{
		if (!$oldData) {
			if (!isset($data['id'])) {
				$data['id'] = $this->packagesData->last['id'];
			}
		}

		return $this->basepackages->activityLogs->addLog($this->packageName, $data, $oldData);
	}

	public function getActivityLogs(int $id, $newFirst = true)
	{
		return $this->basepackages->activityLogs->getLogs($this->packageName, $id, $newFirst);
	}

	protected function useStorage($storageType)
	{
		$storages = $this->basepackages->storages->getAppStorages();

		if ($storages && isset($storages[$storageType])) {//Assign type of storage for uploads
			$this->packagesData->storages = $storages;
			$this->packagesData->storage = $storages[$storageType];
		} else {
			$this->packagesData->storages = [];
		}

		if (!isset($this->domains->domain['apps'][$this->init()->app['id']][$storageType . 'Storage'])) {
			$this->packagesData->storages = [];
		}
	}

	protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null, bool $inclLastData = false, bool $addToLog = true)
	{
		$this->packagesData->responseMessage = $responseMessage;

		$this->packagesData->responseCode = $responseCode;

		if ($responseData !== null && is_array($responseData)) {
			$this->packagesData->responseData = $responseData;
		} else {
			if (isset($this->packagesData->last)) {
				if ($inclLastData) {
					$this->packagesData->responseData = $this->packagesData->last;
				} else {
					$this->packagesData->responseData = ['id' => $this->packagesData->last['id']];
				}
			}
		}

		if ($addToLog) {
			$this->addToLog($responseCode, $responseMessage);
		}
	}

	protected function addToNotification($subscriptionType, $messageTitle, $messageDetails = null)
	{
		$package = $this->checkPackage($this->packageName);

		if ($package) {
			if (isset($this->packagesData->last)) {
				$packageRowId = $this->packagesData->last['id'];
			} else {
				$packageRowId = null;
			}

			if ($package['notification_subscriptions'] && $package['notification_subscriptions'] !== '') {
				$package['notification_subscriptions'] = Json::decode($package['notification_subscriptions'], true);

				if (count($package['notification_subscriptions']) === 0) {
					return;
				}

				foreach ($package['notification_subscriptions'] as $appId => $subscriptions) {
					if (isset($subscriptions[$subscriptionType]) &&
						is_array($subscriptions[$subscriptionType]) &&
						count($subscriptions[$subscriptionType]) > 0
					) {
						foreach ($subscriptions[$subscriptionType] as $key => $aId) {
							$this->basepackages->notifications->addNotification(
								$messageTitle,
								$messageDetails,
								$appId,
								$aId,
								$this->auth->account()['id'],
								$package['name'],
								$packageRowId,
								0
							);
						}
					}
				}
			}
		}
	}

	protected function addToLog($code, $message, $debug = true)
	{
		//Only if debug is enabled.
	}
}