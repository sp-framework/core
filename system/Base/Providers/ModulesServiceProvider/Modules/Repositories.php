<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Mvc\Model\Transaction\Manager;
use System\Base\BasePackage;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Repositories as RepositoriesModel;

class Repositories extends BasePackage implements BasePackageInterface
{
	private $model;

	public $repositories;

	public function getAll(array $conditions = null)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		}

		if (!$this->repositories) {
			$this->model = RepositoriesModel::find($parameters);

			$this->repositories = $this->model->toArray();
		}

		return $this;
	}

	public function get(int $id)
	{
		$parameters = $this->paramsWithCache($this->getIdParams($id));

		$this->model = RepositoriesModel::find($parameters);

		if ($this->model->count() === 1) {
			$this->packagesData->responseCode = 0;
			$this->packagesData->responseMessage = 'Found';

			array_push($this->cacheKeys, $parameters['cache']['key']);

			return $this->model->toArray()[0];

		} else if ($this->model->count() > 1) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';

		} else if ($this->model->count() === 0) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'No Record Found with that ID!';
		}

		$this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

		return false;
	}

	public function add(array $data)
	{
		if ($data) {
			$repository = new RepositoriesModel();

			$repository->assign($data);

			$create = $repository->create();

			$this->resetCache();

			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Repository Updated!';

			return $create;
		}
	}

	public function update(array $data)
	{
		try {
			$txManager = new Manager();
			$transaction = $txManager->get();

			$repository = new RepositoriesModel();

			$repository->setTransaction($transaction);

			$repository->assign($data);

			if (!$repository->update()) {
				$transaction->rollback('Could not update repository.');
			}

			if ($transaction->commit()) {
				//Delete Old cache if exists and generate new cache
				$this->updateCache($data['id']);

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Repository Updated!';

				return true;
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	public function remove(int $id)
	{
		$this->get($id);

		if ($this->model->count() === 1) {
			if ($this->model->delete()) {

				$this->resetCache($id);

				$this->packagesData->responseCode = 0;
				$this->packagesData->responseMessage = 'Repository Deleted!';
				return true;
			} else {
				$this->packagesData->responseCode = 1;
				$this->packagesData->responseMessage = 'Could not delete repository.';
			}
		} else if ($this->model->count() > 1) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
		} else if ($this->model->count() === 0) {
			$this->packagesData->responseCode = 1;
			$this->packagesData->responseMessage = 'No Record Found with that ID!';
		}
	}
}