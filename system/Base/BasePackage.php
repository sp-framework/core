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
	protected $getQueryArr = [];

	public $packagesData;

	protected $app;

	protected $packageName;

	protected $packageNameModel;

	protected $packageNameS;

	protected $model;

	protected $cacheName;

	protected $cacheClass;

	protected $manager = null;

	protected $transaction = null;

	protected $transactionErrors = null;

	private $filterConditions = null;

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;

		$this->setDefaultPackageResponse();

		if (isset($this->modelToUse)) {
			$this->cacheClass = $this->modelToUse;
		} else {
			$this->cacheClass = $this;
		}

		if (!$this->cacheName || $this->config->cache->enabled) {
			$this->getCacheName();
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

		if (!$this->packageNameModel) {
			$this->packageNameModel = $this->packageName . 'Model';
		}

		if (!$this->packageNameS) {
			$this->packageNameS = ucfirst(rtrim($this->packageName, 's'));
		}
	}

	protected function buildGetQueryParamsArr()
	{
		if ($this->request->isGet()) {
			$arr = Arr::chunk($this->dispatcher->getParams(), 2);

			foreach ($arr as $value) {
				if (isset($value[1])) {
					$this->getQueryArr[$value[0]] = $value[1];
				} else {
					$this->getQueryArr[$value[0]] = 0; //Value not set, so default to 0
				}
			}
		}
	}

	protected function getData()
	{
		return $this->getQueryArr;
	}

	protected function postData()
	{
		return $this->request->getPost();
	}

	protected function putData()
	{
		return $this->request->getPut();
	}

	public function getById(int $id, bool $resetCache = false, bool $enableCache = true)
	{
		$this->buildGetQueryParamsArr();

		if ($id) {
			if ($this->config->cache->enabled && !$resetCache && $enableCache && $this->cacheName) {
				$parameters = $this->paramsWithCache($this->getIdParams($id), $this->cacheName);
			} else {
				$parameters = $this->getIdParams($id);
			}

			if (isset($this->getData()['resetcache']) && $this->getData()['resetcache'] == 'true') {
				$this->resetCache($id);
			}

			$this->getFirst(null, null, $resetCache, $enableCache, null, $parameters);

			if ($this->model) {
				return $this->getDbData($parameters, $enableCache);
			}

			return false;
		}

		throw new \Exception('getById needs id parameter to be set.');
	}

	public function getFirst($by = null, $value = null, bool $resetCache = false, bool $enableCache = true, $model = null, $params = [], $returnArray = false)
	{
		$this->useModel($model);

		if ($by && $value && !$params) {
			$params = $this->getParams($by, $value);
		}

		if ($this->config->cache->enabled && !$resetCache && $enableCache && $this->cacheName) {
			$parameters = $this->paramsWithCache($params);
		} else {
			$parameters = $params;
		}

		if ($resetCache) {
			$this->resetCache();
		}

		try {
			$this->model = $this->modelToUse::findFirst($parameters);

			$this->cacheTools->updateIndex(
				$this->cacheName,
				$parameters,
				null,
				true,
				$this->model
			);

			if (!$returnArray) {
				return $this->model;
			} else {
				return $this->model->toArray();
			}

		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function getAll(bool $resetCache = false, bool $enableCache = true, $model = null)
	{
		if (!$this->{$this->packageName} || $resetCache) {
			$this->{$this->packageName} = $this->getByParams(['conditions'=>''], $resetCache, $enableCache, $model);
		}

		return $this;
	}

	public function getByParams(array $params, bool $resetCache = false, bool $enableCache = true, $model = null)
	{
		if (isset($params['conditions'])) {
			if ($this->config->cache->enabled && !$resetCache && $enableCache && $this->cacheName) {
				$parameters = $this->paramsWithCache($params);
			} else {
				$parameters = $params;
			}

			$relationColumns = [];

			if (isset($parameters['columns']) && count($parameters['columns']) > 0) {
				$modelMetaData = $this->getModelsMetaData();
				$modelRelations = $this->getModelsRelations()['modelRelations'];

				foreach ($parameters['columns'] as $columnKey => $column) {
					if (!in_array($column, $modelMetaData['columns'])) {
						if ($modelRelations) {
							foreach ($modelRelations as $modelRelationKey => $modelRelation) {
								if (in_array($column, $modelRelation['columns'])) {
									if (!isset($relationColumns[$modelRelationKey])) {
										$relationColumns[$modelRelationKey] = $modelRelation;
										$relationColumns[$modelRelationKey]['requestedColumns'] = [];
									}
									array_push($relationColumns[$modelRelationKey]['requestedColumns'], $column);
									unset($parameters['columns'][$columnKey]);
								}
							}
						}
					}
				}
			}

			if ($resetCache) {
				$this->resetCache();
			}

			$this->useModel($model);

			$this->model = $this->modelToUse::find($parameters);

			$data = $this->getDbData($parameters, $enableCache, 'params');

			if ($data && count($relationColumns) > 0) {
				$data = $this->getRelationColumnsData($relationColumns, $data);
			}

			if ($data) {
				return $data;
			} else {
				return false;
			}
		}

		throw new \Exception('getByParams needs parameter condition to be set.');
	}

	protected function getRelationColumnsData($relationColumns, $data)
	{
		if (count($relationColumns) === 0) {
			return $data;
		}

		foreach ($data as $dataKey => $row) {
			$model = $this->getFirst('id', $row['id']);

			foreach ($relationColumns as $relationColumnKey => $relationColumn) {
				$alias = $relationColumn['relationObj']->getOption('alias');

				if ($model->{$alias}) {
					$relationRowData = $model->{$alias}->toArray();

					foreach ($relationRowData as $relationRowKey => $relationRow) {
						if (in_array($relationRowKey, $relationColumn['requestedColumns'])) {
							$data[$dataKey][$relationRowKey] = $relationRow;
						}
					}
				}
			}
		}

		return $data;
	}

	public function getPaged(array $params = [], bool $resetCache = false, bool $enableCache = true, $arrayData = false)
	{
		//Empty columns causes SQL error :APL0:
		if (isset($params['columns']) && count($params['columns']) === 0) {
			unset($params['columns']);
		}

		if (isset($this->postData()['page'])) {
			$pageParams['currentPage'] = $this->postData()['page'];
		} else if (isset($params['page'])) {
			$pageParams['currentPage'] = $params['page'];
		} else {
			$pageParams['currentPage'] = 1;
		}

		if (isset($this->postData()['conditions'])) {
			$pageParams['conditions'] = $this->postData()['conditions'];
		} else if (isset($params['conditions'])) {
			$pageParams['conditions'] = $params['conditions'];
		} else {
			$pageParams['conditions'] = '';
		}

		if (isset($this->postData()['limit'])) {
			$pageParams['limit'] = $this->postData()['limit'];
		} else if (isset($params['limit'])) {
			$pageParams['limit'] = $params['limit'];
		} else {
			$pageParams['limit'] = 20;
		}

		if (isset($this->postData()['resetCache']) && $this->postData()['resetCache'] == 'true') {
			$resetCache = true;
		}

		if (isset($this->postData()['order']) &&
			$this->postData()['order'] !== ''
		) {
			$params =
				array_merge(
					$params,
					[
						'order'	=> $this->postData()['order']
					]
				);
		} else if (isset($params['order'])) {
			$params =
				array_merge(
					$params,
					[
						'order'	=> $params['order']
					]
				);
		}

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

		if (!$arrayData &&
			isset($pageParams['conditions']) && $pageParams['conditions'] !== ''
		) {
			$data = $this->getDataWithConditions($params, $pageParams['conditions'], $resetCache, $enableCache);
		} else {
			if (is_array($arrayData)) {
				$data = $arrayData;
			}
		}

		if (!isset($data)) {
			$params =
				array_merge(
					$params,
					[
						'conditions'	=> '',
					]
				);

			$data = $this->getByParams($params, $resetCache, $enableCache);
		}

		// var_dump($data);die();
		if ($data) {
			$pageParams['data'] = $data;
		} else {
			$pageParams['data'] = [];
		}

		try {
			$paginator = new NativeArray($pageParams);

			$paged = $paginator->paginate();

			if (is_array($arrayData)) {
				$paginationCounters['total_items'] = count($arrayData);
			} else {
				$paginationCounters['total_items'] = $this->modelToUse::count();
			}

			if ($this->filterConditions) {
				$paginationCounters['filtered_items'] = $this->modelToUse::count($this->filterConditions);
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
		} catch (\Exception $e) {
			throw $e;
		}
	}

	protected function getDataWithConditions($params, $conditions, bool $resetCache = false, bool $enableCache = true)
	{
		if ($conditions === '') {
			$params =
				array_merge(
					$params,
					[
						'conditions'	=> '',
					]
				);

			return $this->getByParams($params, $resetCache, $enableCache);
		}

		$postConditions = explode('&', rtrim($conditions, '&'));

		$queries = [];
		$multiModel = false;
		$modelColumnMap = $this->getModelsColumnMap();

		foreach ($postConditions as $conditionKey => $condition) {
			$conditionArr = explode('|', $condition);

			if (isset($modelColumnMap['model'][$conditionArr[1]])) {
				$model = Arr::last(explode('\\', $modelColumnMap['model'][$conditionArr[1]]));
				$queries[$model]['model'] = $modelColumnMap['model'][$conditionArr[1]];
			} else {
				$model = Arr::last(explode('\\', $this->modelToUse));
				$queries[$model]['model'] = $this->modelToUse;
			}

			$condition = '';
			$bind = [];

			if ($queries[$model]['model'] !== $this->modelToUse) {
				$multiModel = true;
			}

			if (str_starts_with(strtolower($conditionArr[1]), 'not')) {
				$conditionArr[1] = '[' . $conditionArr[1] . ']';
			}

			if (Arr::firstKey($postConditions) !== $conditionKey) {
				if ($conditionArr[0] === '') {
					$queries[$model]['andor'] = 'AND';//Default for AND/OR
				} else {
					$queries[$model]['andor'] = strtoupper($conditionArr[0]);
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
					$condition .=
						$conditionArr[1] . ' ' . $sign;
				} else if ($conditionArr[2] === 'notbetween') {
					$condition .=
					'NOT ' . $conditionArr[1] . ' BETWEEN';
				}

				foreach ($valueArr as $valueKey => $valueValue) {
					$condition .=
						' :baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

					$bind[
						'baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
					] = $valueValue;

					if (Arr::lastKey($valueArr) !== $valueKey) {
						$condition .= ' AND';
					}
				}
			} else if ($conditionArr[2] === 'empty' || $conditionArr[2] === 'notempty') {
				$condition .= $conditionArr[1] . ' ' . $sign;
			} else {
				$valueArr = explode(',', $conditionArr[3]);

				if (count($valueArr) > 1) {
					foreach ($valueArr as $valueKey => $valueValue) {
						$condition .=
							$conditionArr[1] . ' ' . $sign .
							' :baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

						$bind[
							'baz_' . $conditionKey . '_' . $valueKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
						] = $valueValue;

						if (Arr::lastKey($valueArr) !== $valueKey) {
							$condition .= ' OR ';
						}
					}
				} else {
					$condition .=
						$conditionArr[1] . ' ' . $sign . ' :baz_' . $conditionKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1])) . ':';

					$bind[
						'baz_' . $conditionKey . '_' . str_replace('[', '', str_replace(']', '', $conditionArr[1]))
					] = $valueArr[0];
				}
			}

			if (isset($queries[$model]['condition'])) {
				$queries[$model]['condition'] .= ' ' . $queries[$model]['andor'] . ' ' . $condition;
			} else {
				$queries[$model]['condition'] = $condition;
			}

			if (isset($queries[$model]['bind'])) {
				$queries[$model]['bind'] = array_merge($queries[$model]['bind'], $bind);
			} else {
				$queries[$model]['bind'] = $bind;
			}
		}

		$data = [];

		if (count($queries) > 0) {
			$relationColumns = [];

			if (isset($params['columns']) && count($params['columns']) > 0) {
				$modelMetaData = $this->getModelsMetaData();
				$modelRelations = $this->getModelsRelations()['modelRelations'];

				foreach ($params['columns'] as $columnKey => $column) {
					if (!in_array($column, $modelMetaData['columns'])) {
						if ($modelRelations) {
							foreach ($modelRelations as $modelRelationKey => $modelRelation) {
								if (in_array($column, $modelRelation['columns'])) {
									if (!isset($relationColumns[$modelRelationKey])) {
										$relationColumns[$modelRelationKey] = $modelRelation;
										$relationColumns[$modelRelationKey]['requestedColumns'] = [];
									}
									array_push($relationColumns[$modelRelationKey]['requestedColumns'], $column);
									unset($params['columns'][$columnKey]);
								}
							}
						}
					}
				}
			}

			$rootModelToUse = $this->modelToUse;

			foreach ($queries as $query) {
				if ($query['model'] !== $this->modelToUse) {
					$this->modelToUse = $query['model'];

					$md = $this->getModelsMetaData();

					foreach ($md['columns'] as &$column) {
						if (str_starts_with(strtolower($column), 'not')) {
							$column = '[' . $column . ']';
						}
					}

					$this->filterConditions =
						[
							'conditions'	=> $query['condition'],
							'bind'			=> $query['bind']
						];

					$params = array_merge($params, $this->filterConditions);

					$params['columns'] = $md['columns'];

					$rows = $this->getByParams($params, true, false);

					if ($rows && count($rows) > 0) {
						foreach ($rows as $row) {
							$model = $this->getFirst('id', $row['id'], true, false);

							$rowData = $model->toArray();

							$modelRelations = [];

							if (method_exists($model, 'getModelRelations')) {
								$modelRelations = $model->getModelRelations();
							}

							if ($modelRelations && count($modelRelations) > 0) {
								foreach ($modelRelations as $relationColumnKey => $relationColumn) {
									if ($relationColumn['relationObj']->getType() === 0) {
										$parentFields = $relationColumn['relationObj']->getFields();
										$parentReferencedFields = $relationColumn['relationObj']->getReferencedFields();
										$parentModelClass = $relationColumn['relationObj']->getReferencedModel();

										break;
									}
								}
							}

							if (isset($parentModelClass) && is_string($parentFields) && is_string($parentReferencedFields)) {
								$parentModel = $this->getFirst($parentReferencedFields, $rowData[$parentFields], true, false, $parentModelClass);
							} else if (isset($parentModelClass) && is_array($parentFields) && is_array($parentReferencedFields)) {
								// Params
								// $parentModel = $this->getFirst($parentReferencedFields, $rowData[$parentFields], true, false, $parentModelClass);
							}

							if (isset($parentModel)) {
								$rowData = array_merge($rowData, $parentModel->toArray());

								if (method_exists($parentModel, 'getModelRelations')) {
									$parentModelRelations = $parentModel->getModelRelations();
								}

								if (isset($parentModelRelations) && is_array($parentModelRelations) && count($parentModelRelations) > 0) {
									foreach ($parentModelRelations as $parentModelRelationKey => $parentModelRelation) {
										if ($parentModelRelation['relationObj']->getReferencedModel() !== $this->modelToUse) {
											$childrenAlias = $parentModelRelation['relationObj']->getOption('alias');

											if (isset($parentModel->{$childrenAlias}) && $parentModel->{$childrenAlias}) {
												$childrenRowData = $parentModel->{$childrenAlias}->toArray();

												unset($childrenRowData['id']);

												$rowData = array_merge($rowData, $childrenRowData);
											}
										}
									}
								}

								if (count($rowData) > 0) {
									array_push($data, $rowData);
								}
							} else {
								$rowData = array_merge($rowData, $model->toArray());

								if (count($rowData) > 0) {
									array_push($data, $rowData);
								}
							}
						}
					}
				} else {
					$md = $this->getModelsMetaData();

					foreach ($md['columns'] as &$column) {
						if (str_starts_with(strtolower($column), 'not')) {
							$column = '[' . $column . ']';
						}
					}

					$this->filterConditions =
						[
							'conditions'	=> $query['condition'],
							'bind'			=> $query['bind']
						];

					$params = array_merge($params, $this->filterConditions);

					$params['columns'] = $md['columns'];

					$rows = $this->getByParams($params, true, false);

					if ($rows && count($rows) > 0) {
						foreach ($rows as $row) {
							$rootModel = $this->getFirst('id', $row['id']);

							if ($rootModel) {
								$rowData = $rootModel->toArray();
							}

							$model = $this->getFirst('id', $row['id'], true, false, $query['model']);

							$modelRelations = [];

							if (method_exists($model, 'getModelRelations')) {
								$modelRelations = $model->getModelRelations();
							}

							if ($modelRelations && count($modelRelations) > 0) {
								foreach ($modelRelations as $relationColumnKey => $relationColumn) {
									if (isset($relationColumns[$relationColumnKey])) {
										$alias = $relationColumn['relationObj']->getOption('alias');

										if (isset($rootModel->{$alias}) && $rootModel->{$alias}) {
											$relationRowData = $rootModel->{$alias}->toArray();
											unset($relationRowData['id']);
											$rowData = array_merge($rowData, $relationRowData);
										}
									}
								}
								if (count($rowData) > 0) {
									array_push($data, $rowData);
								}
							} else {
								$rowData = array_merge($rowData, $model->toArray());
								if (count($rowData) > 0) {
									array_push($data, $rowData);
								}
							}
						}
					}
				}
			}
		}

		return $data;
	}

	protected function getDbData($parameters, bool $enableCache = true, string $type = 'id', bool $returnArray = true)
	{
		if ($type === 'id') {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Found';

			if ($enableCache && $this->cacheName) {
				$this->cacheTools->updateIndex(
					$this->cacheName,
					$parameters,
					null,
					true,
					$this->model
				);
			}

			return $this->model->toArray();

		} else if ($type === 'params') {
			if ($enableCache && $this->cacheName) {
				$this->cacheTools->updateIndex(
					$this->cacheName,
					$parameters,
					true,
					null,
					$this->model
				);
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

	public function add(array $data, $resetCache = true)
	{
		if ($data) {
			$data = $this->jsonData($data);

			${$this->packageNameModel} = $this->useModel();

			${$this->packageNameModel}->assign($data);

			$create = ${$this->packageNameModel}->create();

			if ($create) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = "Added " . ucfirst($this->packageNameS) . "!";

				$this->packagesData->last = ${$this->packageNameModel}->toArray();

				if ($resetCache) {
					$this->resetCache();
				}

				return true;
			} else {
				$this->transactionErrors = [];

				foreach (${$this->packageNameModel}->getMessages() as $value) {
					array_push($this->transactionErrors, $value->getMessage());
				}

				array_push($this->transactionErrors, $data);

				throw new \Exception(
					"Could not add " . ucfirst($this->packageNameS) . "Reasons: <br>" .
					join(',', $this->jsonData($this->transactionErrors))
				);
			}
		} else {
			throw new \Exception('Data array missing. Cannot add!');
		}
	}

	public function update(array $data, $resetCache = true)
	{
		if ($data) {
			$data = $this->jsonData($data);

			${$this->packageNameModel} = $this->getFirst('id', $data['id'], false, false);

			if (!${$this->packageNameModel}) {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'ID: ' . $data['id'] . " not found for package {$this->packageName}";

				return;
			}

			${$this->packageNameModel}->assign($data);

			$update = ${$this->packageNameModel}->update();

			if ($update) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = ucfirst($this->packageNameS) . " Updated!";

				$this->packagesData->last = ${$this->packageNameModel}->toArray();

				if ($resetCache && count(${$this->packageNameModel}->getUpdatedFields()) !== 0) {//Make sure we only update when we change any fields
					$this->resetCache($this->packagesData->last['id']);
				}

				return true;
			} else {
				$this->transactionErrors = [];

				foreach (${$this->packageNameModel}->getMessages() as $value) {
					array_push($this->transactionErrors, $value->getMessage());
				}

				throw new \Exception(
					"Could not update " . ucfirst($this->packageNameS) . "Reasons: <br>" .
					join(',', $this->transactionErrors)
				);
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

	public function remove(int $id, $resetCache = true, $removeRelated = true, $removeRelatedAliases = [])
	{
		$this->getFirst('id', $id);

		if ($this->model && $this->model->id == $id) {

			$relationsDeleted = true;

			if ($removeRelated) {
				$modelRelations = $this->getModelsRelations();

				if (isset($modelRelations['modelRelations']) &&
					is_array($modelRelations['modelRelations']) &&
					count($modelRelations['modelRelations']) > 0
				) {
					$relationsDeleted = true;

					foreach ($modelRelations['modelRelations'] as $modelRelationKey => $modelRelation) {
						$type = $modelRelation['relationObj']->getType();

						if ($type !== 0) {//Other than belongsTo
							$removeAlias = false;

							$alias = $modelRelation['relationObj']->getOption('alias');

							if (count($removeRelatedAliases) > 0 && in_array($alias, $removeRelatedAliases)) {
								$removeAlias = true;
							} else if (count($removeRelatedAliases) === 0) {
								$removeAlias = true;
							}

							if ($removeAlias && $this->model->{$alias}) {
								$relationRowsData = $this->model->{$alias}->toArray();

								if (!$this->model->{$alias}->delete()) {
									$relationsDeleted = false;
								} else {
									if ($resetCache &&
										count($relationRowsData) > 0 &&
										isset($relationRowsData['id'])
									) {
										$cacheName = $this->extractCacheName($modelRelation['relationObj']->getReferencedModel());
										$this->resetCache($relationRowsData['id'], true, $cacheName);
									}
								}
							}
						}
					}
				}
			}

			if ($relationsDeleted && $this->model->delete()) {
				if ($resetCache) {
					$this->resetCache($id, true);
				}

				$this->addResponse(ucfirst($this->packageNameS) . " Deleted!");

				return true;
			} else {
				$this->addResponse("Could not delete " . ucfirst($this->packageNameS), 1);
			}
		} else {
			$this->addResponse("No Record Found with that ID!", 1);
		}

		return false;
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

	protected function getParams($by, $value)
	{
		return
			[
				'conditions'	=> $by . ' = :' . $by . ':',
				'bind'			=>
					[
						$by		=> $value
					]
			];
	}

	protected function getClassName()
	{
		$reflection = new \ReflectionClass($this);

		return explode('\\', $reflection->getName());
	}

	protected function getCacheClassName($cacheClass = null)
	{
		if (!$cacheClass) {
			$cacheClass = $this->cacheClass;
		}

		$reflection = new \ReflectionClass($cacheClass);

		return explode('\\', $reflection->getName());
	}

	protected function extractCacheName($cacheClass = null)
	{
		$class = $this->getCacheClassName($cacheClass);

		return strtolower(join($class));
	}

	public function setCacheName($key)
	{
		$this->cacheName = $key;

		return $this->cacheName;
	}

	public function getCacheName()
	{
		if (!$this->cacheName) {
			return $this->setCacheName($this->extractCacheName());
		}

		return $this->cacheName;
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

	protected function useModel($model = null)
	{
		if (!$model) {
			return new $this->modelToUse;
		}

		return new $model;
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
		return $this->cacheTools->addModelCacheParameters($params, $this->cacheName);
	}

	protected function resetCache(int $id = null, $removeId = false, $cacheName = null)
	{
		if (!$cacheName) {
			$cacheName = $this->cacheName;
		}

		$this->cacheTools->resetCache($cacheName, $id, $removeId);
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
			$model = $this->useModel();

			$md = [];

			$metadata = $model->getModelsMetaData();

			$md['dataTypes'] = $metadata->getDataTypes($model);
			$md['number'] = $metadata->getDataTypesNumeric($model);
			$md['columns'] = $metadata->getAttributes($model);
			$md['required'] = $metadata->getNotNullAttributes($model);
			$md['columnSize'] = [];
			$md['columnUnique'] = [];

			$tableDescription = $this->describe($model->getSource());

			if ($tableDescription && count($tableDescription) > 0) {
				foreach ($tableDescription as $column) {
					$md['columnSize'][$column->getName()] = $column->getSize();
				}
			}

			$tableDescription = $this->describe($model->getSource(), true);

			if ($tableDescription && count($tableDescription) > 0) {
				foreach ($tableDescription as $index) {
					if (strtoupper($index->getType()) === 'UNIQUE') {
						$uniqueColumns = $index->getColumns();

						foreach ($uniqueColumns as $uniqueColumn) {
							$md['columnUnique'][$uniqueColumn] = $uniqueColumn;
						}
					}
				}
			}

			return $md;
		}

		return [];
	}

	protected function getModelsRelations()
	{
		if ($this->modelToUse) {
			$model = $this->useModel();

			$relations = [];
			$relations['dataTypes'] = [];
			$relations['number'] = [];
			$relations['columns'] = [];
			$relations['required'] = [];
			$relations['columnSize'] = [];
			$relations['columnUnique'] = [];
			$relations['modelRelations'] = [];

			if (method_exists($model, 'getModelRelations')) {
				$relations['modelRelations'] = $model->getModelRelations();
			}

			if ($relations['modelRelations'] && is_array($relations['modelRelations']) && count($relations['modelRelations']) > 0) {
				foreach ($relations['modelRelations'] as $modelRelationKey => $modelRelation) {
					$referencedModel = $modelRelation['relationObj']->getReferencedModel();

					$model = new $referencedModel();

					$md = $model->getModelsMetaData();

					$fields = $modelRelation['relationObj']->getFields();
					$referencedFields = $modelRelation['relationObj']->getReferencedFields();
					$intermediateFields = $modelRelation['relationObj']->getIntermediateFields();

					$dataTypes = $md->getDataTypes($model);
					$number = $md->getDataTypesNumeric($model);
					$columns = $md->getAttributes($model);
					$required = $md->getNotNullAttributes($model);
					$columnSize = [];
					$columnUnique = [];

					$tableDescription = $this->describe($model->getSource());

					if ($tableDescription && count($tableDescription) > 0) {
						foreach ($tableDescription as $column) {
							$columnSize[$column->getName()] = $column->getSize();
						}
					}

					$tableDescription = $this->describe($model->getSource(), true);

					if ($tableDescription && count($tableDescription) > 0) {
						foreach ($tableDescription as $index) {
							if (strtoupper($index->getType()) === 'UNIQUE') {
								$uniqueColumns = $index->getColumns();

								foreach ($uniqueColumns as $uniqueColumn) {
									$columnUnique[$uniqueColumn] = $uniqueColumn;
								}
							}
						}
					}

					if (is_array($fields)) {
						foreach ($fields as $fieldKey => $field) {
							unset($dataTypes[$field]);
							unset($number[$field]);
							$key = array_search($field, $columns);
							unset($columns[$key]);
							unset($required[$key]);
							unset($columnSize[$key]);
							unset($columnUnique[$key]);
						}
					} else {
						unset($dataTypes[$fields]);
						unset($number[$fields]);
						$key = array_search($fields, $columns);
						unset($columns[$key]);
						unset($required[$key]);
						unset($columnSize[$key]);
						unset($columnUnique[$key]);
					}

					if (is_array($referencedFields)) {
						foreach ($referencedFields as $referencedFieldKey => $referencedField) {
							unset($dataTypes[$referencedField]);
							unset($number[$referencedField]);
							$key = array_search($referencedField, $columns);
							unset($columns[$key]);
							unset($required[$key]);
							unset($columnSize[$key]);
							unset($columnUnique[$key]);
						}
					} else {
						unset($dataTypes[$referencedFields]);
						unset($number[$referencedFields]);
						$key = array_search($referencedFields, $columns);
						unset($columns[$key]);
						unset($required[$key]);
						unset($columnSize[$key]);
						unset($columnUnique[$key]);
					}

					if (is_array($intermediateFields)) {
						foreach ($intermediateFields as $intermediateFieldKey => $intermediateField) {
							unset($dataTypes[$intermediateField]);
							unset($number[$intermediateField]);
							$key = array_search($intermediateField, $columns);
							unset($columns[$key]);
							unset($required[$key]);
							unset($columnSize[$key]);
							unset($columnUnique[$key]);
						}
					} else {
						unset($dataTypes[$intermediateFields]);
						unset($number[$intermediateFields]);
						$key = array_search($intermediateFields, $columns);
						unset($columns[$key]);
						unset($required[$key]);
						unset($columnSize[$key]);
						unset($columnUnique[$key]);
					}

					$relations['dataTypes'] = array_merge($relations['dataTypes'], $dataTypes);
					$relations['modelRelations'][$modelRelationKey]['dataTypes'] = $dataTypes;
					$relations['number'] = array_merge($relations['number'], $number);
					$relations['modelRelations'][$modelRelationKey]['number'] = $number;
					$relations['columns'] = array_merge($relations['columns'], $columns);
					$relations['modelRelations'][$modelRelationKey]['columns'] = $columns;
					$relations['required'] = array_merge($relations['required'], $required);
					$relations['modelRelations'][$modelRelationKey]['required'] = $required;
					$relations['columnSize'] = array_merge($relations['columnSize'], $columnSize);
					$relations['modelRelations'][$modelRelationKey]['columnSize'] = $columnSize;
					$relations['columnUnique'] = array_merge($relations['columnUnique'], $columnUnique);
					$relations['modelRelations'][$modelRelationKey]['columnUnique'] = $columnUnique;
				}
			}

			return $relations;
		}

		return [];
	}

	public function getModelsColumnMap(array $filter = [])
	{
		$metadata = [];

		$md = $this->getModelsMetaData();

		$md['model'] = [];
		$md['model']['id'] = $this->modelToUse;

		if (count($md) > 0) {
			$filteredColumns = [];

			$rmdArr = $this->getModelsRelations();

			if ($rmdArr &&
				count($rmdArr['dataTypes']) > 0 &&
				count($rmdArr['number']) > 0 &&
				count($rmdArr['columns']) > 0 &&
				count($rmdArr['modelRelations']) > 0
			) {
				foreach ($rmdArr as $rmdKey => $rmd) {
					if ($rmdKey === 'dataTypes') {
						if (count($rmd) > 0) {
							$md['dataTypes'] = array_merge($md['dataTypes'], $rmd);
						}
					}
					if ($rmdKey === 'number') {
						if (count($rmd) > 0) {
							$md['number'] = array_merge($md['number'], $rmd);
						}
					}
					if ($rmdKey === 'columns') {
						if (count($rmd) > 0) {
							foreach ($md['columns'] as $mdc) {
								$md['model'][$mdc] = $this->modelToUse;
							}

							$md['columns'] = array_merge($md['columns'], $rmd);
						}
					}
					if ($rmdKey === 'required') {
						if (count($rmd) > 0) {
							$md['required'] = array_merge($md['required'], $rmd);
						}
					}
					if ($rmdKey === 'columnSize') {
						if (count($rmd) > 0) {
							$md['columnSize'] = array_merge($md['columnSize'], $rmd);
						}
					}
					if ($rmdKey === 'columnUnique') {
						if (count($rmd) > 0) {
							$md['columnUnique'] = array_merge($md['columnUnique'], $rmd);
						}
					}
					if ($rmdKey === 'modelRelations') {
						if (count($rmd) > 0) {
							foreach ($rmd as $rmdModel) {
								$model = $rmdModel['relationObj']->getReferencedModel();

								foreach ($rmdModel['columns'] as $rmdModelColumn) {
									$md['model'][$rmdModelColumn] = $model;
								}
							}
						}
					}
				}
			}

			if (count($filter) > 0) {
				foreach ($md['columns'] as $column) {
					if (in_array($column, $filter)) {
						array_push($filteredColumns, $column);
					}
				}
			}

			if (count($filteredColumns) > 0) {
				foreach ($filteredColumns as $filteredColumn) {
					$metadata[$filteredColumn]['id'] = $filteredColumn;
					$metadata[$filteredColumn]['name'] = str_replace('_', ' ', $filteredColumn);

					if (isset($md['number'][$filteredColumn]) && $md['number'][$filteredColumn] === true) {
						$metadata[$filteredColumn]['data']['number'] = 'true';
					} else {
						$metadata[$filteredColumn]['data']['number'] = 'false';
					}

					$metadata[$filteredColumn]['data']['dataType'] = $md['dataTypes'][$filteredColumn];

					// if (isset($md['model'][$filteredColumn])) {
					// 	$metadata[$filteredColumn]['data']['model'] = $md['model'][$filteredColumn];
					// }
				}

				return $metadata;
			}

			return $md;
		}

		return false;
	}

	protected function executeSQL(string $sql, $data = [])
	{
		try {
			return $this->db->query($sql, $data);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}

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

	public function dropTable(string $table)
	{
		$this->db->dropTable($table);
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

	public function alterTable(string $method, string $table, array $columns, $schemaName = '')
	{
		$method = $method . 'Column';

		try {
			foreach ($columns as $column) {
				$this->db->{$method}(
					$table,
					$schemaName,
					$column
				);
			}

			return true;
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function addIndex(string $table, array $index, $schemaName = '')
	{
		foreach ($index as $idx) {
			$columnsArr = $idx->getColumns();

			if (count($columnsArr) > 1) {
				$columns = '';

				foreach ($columnsArr as $columnsArrKey => $column) {
					$columns .= '`' . $column . '`';

					if ($columnsArrKey != Arr::lastKey($columnsArr)) {
						$columns .= ',';
					}
				}
			} else {
				$columns = '`' . $columnsArr[0] . '`';
			}

			$this->executeSQL(
				'ALTER TABLE `' . $table . '` ADD ' . strtoupper($idx->getType()) . ' `' . $idx->getName() . '` (' . $columns . ')'
			);
		}
	}

	public function dropIndex(string $table, string $indexName, $schemaName = '')
	{
		try {
			return $this->db->dropIndex($table, $schemaName, $indexName);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
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

	public function getActivityLogs(int $id, $newFirst = true, $page = 1, $packageName = null)
	{
		if ($packageName) {
			return $this->basepackages->activityLogs->getLogs($packageName, $id, $newFirst, $page);
		}

		return $this->basepackages->activityLogs->getLogs($this->packageName, $id, $newFirst, $page);
	}

	public function getNoteLogs(int $id, $newFirst = true, $page = 1, $packageName = null)
	{
		if ($packageName) {
			return $this->basepackages->notes->getNotes($packageName, $id, $newFirst, $page);
		}

		return $this->basepackages->notes->getNotes($this->packageName, $id, $newFirst, $page);
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
			} else {
				$this->packagesData->responseData = [];
			}
		}

		if ($addToLog) {
			$this->addToLog($responseCode, $responseMessage);
		}
	}

	protected function addToNotification($subscriptionType, $messageTitle, $messageDetails = null, $package = null, $packageRowId = null)
	{
		if (!$this->app) {
			$this->app = $this->apps->getAppInfo();
		}

		if (!$package) {
			$package = $this->checkPackage($this->packageName);
		} else if ($package && is_string($package)) {
			$package = $this->checkPackage($package);
		}

		if ($package) {
			if (!$packageRowId && isset($this->packagesData->last)) {
				$packageRowId = $this->packagesData->last['id'];
			} else if (!$packageRowId) {
				$packageRowId = null;
			}

			if ($package['notification_subscriptions'] && $package['notification_subscriptions'] !== '') {
				$package['notification_subscriptions'] = Json::decode($package['notification_subscriptions'], true);

				if (count($package['notification_subscriptions']) === 0) {
					return;
				}

				foreach ($package['notification_subscriptions'] as $appId => $subscriptions) {
					if ($subscriptionType === 'add' || $subscriptionType === 'update' || $subscriptionType === 'remove') {
						$notificationType = '0';
					} else if ($subscriptionType === 'warning') {
						$notificationType = '1';
					} else if ($subscriptionType === 'error') {
						$notificationType = '2';
					}

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
								null,
								$package['name'],
								$packageRowId,
								$notificationType
							);
						}
					}

					if (isset($subscriptions['email']) && count($subscriptions['email']) > 0) {
						$domainId = '1';//Default Domain for system generated Notifications (like API)

						if ($this->packageName === 'domains') {
							if ($this->domain) {
								$domainId = $this->domain['id'];
							}
						} else {
							if ($this->domains && $this->domains->getDomain()) {
								$domainId = $this->domains->getDomain()['id'];
							}
						}

						$this->basepackages->notifications->emailNotification(
							$subscriptions['email'],
							$messageTitle,
							$messageDetails,
							$domainId,
							$appId,
							null,
							$package['name'],
							$packageRowId,
							$notificationType
						);
					}
				}
			}
		// } else {
		// 	throw new \Exception('Package ' . $this->packageName . ' for notification not found');
		}
	}

	protected function addToLog($code, $message, $debug = true)
	{
		//Only if debug is enabled.
	}

	protected function addRefId($data)
	{
		if (!isset($data['ref_id'])) {
			$data['ref_id'] = null;
		}

		if (!$data['ref_id'] || $data['ref_id'] === '') {
			if (isset($data['entity_id'])) {
				$packageName = Arr::last($this->getClassName());

				$entitiesPackage = new \Apps\Dash\Packages\Business\Entities\Entities;

				$entities = $entitiesPackage->getAll()->entities;

				if ($entities && count($entities) > 0) {
					foreach ($entities as $entityKey => $entity) {
						if ($entity['id'] === $data['entity_id']) {
							$entityId = $entity['id'];
							break;
						}
					}
				} else {
					$entityId = 0;
				}

				if (isset($entities[$entityId])) {
					$settings = $entities[$entityId]['settings'];

					if (isset($settings['prefix-seq'][$packageName])) {
						$settings = $settings['prefix-seq'][$packageName];

						$model = $this->useModel();

						$table = $model->getSource();

						if ($settings['prefix'] !== '') {
							$settings['prefix'] = explode('%', $settings['prefix']);

							$prefixValue = '';
							foreach ($settings['prefix'] as $prefix) {
								if ($prefix === 'Y') {
									$prefixValue .= date('Y');
								} else if ($prefix === 'm') {
									$prefixValue .= date('m');
								} else if ($prefix === 'd') {
									$prefixValue .= date('d');
								} else {
									$prefixValue .= $prefix;
								}
							}

							$currentId = (int) $data['id'];

							if (isset($settings['next_seq_number'])) {
								$nextSeqNumber = (int) $settings['next_seq_number'];

								if ($nextSeqNumber > 0) {

									if ($nextSeqNumber > $currentId) {
										$prefixValue .= $nextSeqNumber;

										$sql = "UPDATE `{$table}` SET `id` = ? WHERE `{$table}`.`id` = ?";

										$this->db->execute($sql, [$nextSeqNumber, $currentId]);

										$data['id'] = $nextSeqNumber;
									} else {
										$prefixValue .= $currentId;
									}
								} else {
									$prefixValue .= $currentId;
								}
							} else {
								$prefixValue .= $currentId;
							}

							$sql = "UPDATE `{$table}` SET `ref_id` = ? WHERE `{$table}`.`id` = ?";

							$this->db->execute($sql, [$prefixValue, $data['id']]);

							$data['ref_id'] = $prefixValue;
						}
					}
				}
			}
		}

		return $data;
	}

	protected function extractNumbers($string)
	{
		return preg_replace('/[^0-9]/', '', $string);
	}
}