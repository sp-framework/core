<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Middlewares as MiddlewaresModel;

class Middlewares extends BasePackage
{
	protected $modelToUse = MiddlewaresModel::class;

	public $middlewares;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getNamedMiddlewareForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($middleware) use ($name, $applicationId) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);
					if ($middleware['applications'][$applicationId]['installed'] === true &&
						$middleware['name'] === ucfirst($name)
					) {
						return $middleware;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate middleware name found for middleware ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getMiddlewaresForApplication($applicationId)
	{
		$filters =
			$this->model->filter(
				function($middleware) use ($applicationId) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);
					if (isset($middleware['applications'][$applicationId]['installed']) &&
						$middleware['applications'][$applicationId]['installed'] === true
					) {
						return $middleware;
					}
				}
			);

		$middlewares = [];

		foreach ($filters as $key => $filter) {
			$middlewares[$key] = $filter;
			$middlewares[$key]['sequence'] = $filter['applications'][$applicationId]['sequence'];
			$middlewares[$key]['enabled'] = $filter['applications'][$applicationId]['enabled'];
		}

		return $middlewares;
	}

	public function updateMiddlewares(array $data)
	{
		foreach ($data['middlewares'] as $middlewareId => $status) {
			$middleware = [];
			$middleware['id'] = $middlewareId;
			if ($status === true) {
				$middleware['enabled'] = 1;
			} else if ($status === false) {
				$middleware['enabled'] = 0;
			}
			$this->update($middleware);
		}

		foreach ($data['sequence'] as $sequence => $middlewareId) {
			$middleware = [];
			$middleware['id'] = $middlewareId;
			$middleware['sequence'] = $sequence;
			$this->update($middleware);
		}

		return true;
	}

	// public function getAll($params = [], bool $resetCache = false)
	// {
	// 	if ($this->cacheKey) {
	// 		$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
	// 	}

	// 	if (!$this->middlewares || $resetCache) {

	// 		$this->model = MiddlewaresModel::find($parameters);

	// 		$this->middlewares = $this->model->toArray();
	// 	}

	// 	return $this;
	// }

	// public function get(int $id, bool $resetCache = false)
	// {
	// 	$parameters = $this->paramsWithCache($this->getIdParams($id));

	// 	$this->model = MiddlewaresModel::find($parameters);

	// 	if ($this->model->count() === 1) {
	// 		$this->packagesData->responseCode = 0;
	// 		$this->packagesData->responseMessage = 'Found';

	// 		array_push($this->cacheKeys, $parameters['cache']['key']);

	// 		return $this->model->toArray()[0];

	// 	} else if ($this->model->count() > 1) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';

	// 	} else if ($this->model->count() === 0) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'No Record Found with that ID!';
	// 	}

	// 	$this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

	// 	return false;
	// }

	// public function add(array $data)
	// {
	// 	try {
	// 		$txManager = new Manager();
	// 		$transaction = $txManager->get();

	// 		$middleware = new MiddlewaresModel();

	// 		$middleware->setTransaction($transaction);

	// 		$middleware->assign($data);

	// 		$create = $middleware->create();

	// 		if (!$create) {
	// 			$transaction->rollback('Could not add middleware.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			$this->resetCache();

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Added middleware!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function update(array $data)
	// {
	// 	try {
	// 		$txManager = new Manager();
	// 		$transaction = $txManager->get();

	// 		$middleware = new MiddlewaresModel();

	// 		$middleware->setTransaction($transaction);

	// 		$middleware->assign($data);

	// 		if (!$middleware->update()) {
	// 			$transaction->rollback('Could not update middleware.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			//Delete Old cache if exists and generate new cache
	// 			$this->updateCache($data['id']);

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Middleware Updated!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function remove(int $id)
	// {
	// 	//Need to solve dependencies for removal
	// 	// $this->get($id);

	// 	// if ($this->model->count() === 1) {
	// 	// 	if ($this->model->delete()) {

	// 	// 		$this->resetCache($id);

	// 	// 		$this->packagesData->responseCode = 0;
	// 	// 		$this->packagesData->responseMessage = 'Middleware Deleted!';
	// 	// 		return true;
	// 	// 	} else {
	// 	// 		$this->packagesData->responseCode = 1;
	// 	// 		$this->packagesData->responseMessage = 'Could not delete middleware.';
	// 	// 	}
	// 	// } else if ($this->model->count() > 1) {
	// 	// 	$this->packagesData->responseCode = 1;
	// 	// 	$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
	// 	// } else if ($this->model->count() === 0) {
	// 	// 	$this->packagesData->responseCode = 1;
	// 	// 	$this->packagesData->responseMessage = 'No Record Found with that ID!';
	// 	// }
	// }
}