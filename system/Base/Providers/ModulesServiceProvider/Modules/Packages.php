<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Packages as PackagesModel;;

class Packages extends BasePackage
{
	protected $modelToUse = PackagesModel::class;

	public $packages;

	public function init()
	{
		$this->getAll();

		return $this;
	}

	// public function getAll($params = [], bool $resetCache = false)
	// {
	// 	if ($this->cacheKey) {
	// 		$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
	// 	}

	// 	if (!$this->packages || $resetCache) {

	// 		$this->model = PackagesModel::find($parameters);

	// 		$this->packages = $this->model->toArray();
	// 	}

	// 	return $this;
	// }

	// public function get(int $id, bool $resetCache = false)
	// {
	// 	$parameters = $this->paramsWithCache($this->getIdParams($id));

	// 	$this->model = PackagesModel::find($parameters);

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

	// 		$package = new PackagesModel();

	// 		$package->setTransaction($transaction);

	// 		$package->assign($data);

	// 		$create = $package->create();

	// 		if (!$create) {
	// 			$transaction->rollback('Could not add package.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			$this->resetCache();

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Added package!';

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

	// 		$package = new PackagesModel();

	// 		$package->setTransaction($transaction);

	// 		$package->assign($data);

	// 		if (!$package->update()) {
	// 			$transaction->rollback('Could not update package.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			//Delete Old cache if exists and generate new cache
	// 			$this->updateCache($data['id']);

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Package Updated!';

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
	// 	// 		$this->packagesData->responseMessage = 'Package Deleted!';
	// 	// 		return true;
	// 	// 	} else {
	// 	// 		$this->packagesData->responseCode = 1;
	// 	// 		$this->packagesData->responseMessage = 'Could not delete package.';
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