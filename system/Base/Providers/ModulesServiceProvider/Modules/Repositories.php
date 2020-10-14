<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Repositories as RepositoriesModel;

class Repositories extends BasePackage
{
	protected $modelToUse = RepositoriesModel::class;

	protected $packageNameS = 'Repository';

	public $repositories;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	// public function getAll($params = [], bool $resetCache = false)
	// {
	// 	if ($this->cacheKey) {
	// 		$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
	// 	}

	// 	if (!$this->repositories || $resetCache) {
	// 		$this->model = RepositoriesModel::find($parameters);

	// 		$this->repositories = $this->model->toArray();
	// 	}

	// 	return $this;
	// }

	// public function get(int $id, bool $resetCache = false)
	// {
	// 	$parameters = $this->paramsWithCache($this->getIdParams($id));

	// 	$this->model = RepositoriesModel::find($parameters);

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

	// 		$repository = new RepositoriesModel();

	// 		$repository->setTransaction($transaction);

	// 		$repository->assign($data);

	// 		$create = $repository->create();

	// 		if (!$create) {
	// 			$transaction->rollback('Could not add repository.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			$this->resetCache();

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Added repository!';

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

	// 		$repository = new RepositoriesModel();

	// 		$repository->setTransaction($transaction);

	// 		$repository->assign($data);

	// 		if (!$repository->update()) {
	// 			$transaction->rollback('Could not update repository.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			//Delete Old cache if exists and generate new cache
	// 			$this->updateCache($data['id']);

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Repository Updated!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function remove(int $id)
	// {
	// 	$this->getById($id);

	// 	if ($this->model->count() === 1) {
	// 		if ($this->model->delete()) {

	// 			$this->resetCache($id);

	// 			$this->packagesData->responseCode = 0;
	// 			$this->packagesData->responseMessage = 'Repository Deleted!';
	// 			return true;
	// 		} else {
	// 			$this->packagesData->responseCode = 1;
	// 			$this->packagesData->responseMessage = 'Could not delete repository.';
	// 		}
	// 	} else if ($this->model->count() > 1) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
	// 	} else if ($this->model->count() === 0) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'No Record Found with that ID!';
	// 	}
	// }
}