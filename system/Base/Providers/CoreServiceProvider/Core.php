<?php

namespace System\Base\Providers\CoreServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Model\Core as CoreModel;

class Core extends BasePackage
{
	protected $modelToUse = CoreModel::class;

	public $core;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->core = $this->core[0];

		$this->core['settings'] = Json::decode($this->core['settings'], true);

		if (isset($this->core['settings']['sigKey']) &&
			isset($this->core['settings']['sigText']) &&
			isset($this->core['settings']['cookiesSig'])
		) {
			$sigKey = $this->core['settings']['sigKey'];
			$sigText = $this->core['settings']['sigText'];
			$cookiesSig = $this->core['settings']['cookiesSig'];
		} else {
			$this->core['settings']['sigKey'] = $sigKey = $this->random->base58();
			$this->core['settings']['sigText'] = $sigText = $this->random->base58(32);
			$this->core['settings']['cookiesSig'] = $cookiesSig = $this->crypt->encryptBase64($sigText, $sigKey);
			$coreData['settings'] = Json::encode($this->core['settings']);
			$this->update($coreData);
		}

		return $this;
	}

	// public function getAll($params = [], bool $resetCache = false)
	// {
	// 	if ($this->cacheKey) {
	// 		$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());
	// 	}

	// 	if (!$this->core || $resetCache) {

	// 		$this->model = CoreModel::find($parameters);

	// 		$this->core = $this->model->toArray();
	// 	}

	// 	return $this;
	// }

	// public function get(int $id, bool $resetCache = false)
	// {
	// 	$parameters = $this->paramsWithCache($this->getIdParams($id));

	// 	$this->model = CoreModel::find($parameters);

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

	// 		$repository = new CoreModel();

	// 		$repository->setTransaction($transaction);

	// 		$repository->assign($data);

	// 		$create = $repository->create();

	// 		if (!$create) {
	// 			$transaction->rollback('Could not add core.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			$this->resetCache();

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Added core!';

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

	// 		$core = new CoreModel();

	// 		$core->setTransaction($transaction);

	// 		$core->assign($data);

	// 		if (!$core->update()) {
	// 			$transaction->rollback('Could not update core.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			//Delete Old cache if exists and generate new cache
	// 			$this->updateCache($data['id']);

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Core Updated!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function remove(int $id)
	// {
	// 	//
	// }
}