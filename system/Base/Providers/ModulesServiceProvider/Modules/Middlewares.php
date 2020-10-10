<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Middlewares as MiddlewaresModel;

class Middlewares extends BasePackage implements BasePackageInterface
{
	private $model;

	public $middlewares;

	public function getAll($conditions = null)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		}

		if (!$this->middlewares) {

			$this->model = MiddlewaresModel::find($parameters);

			$this->middlewares = $this->model->toArray();
		}

		return $this;
	}

	public function add(array $data)
	{
		if ($data) {
			$middleware = new MiddlewaresModel();

			return $middleware->add($data, $this->cacheKey);
		}
	}

	public function update(array $data)
	{
		//
	}

	public function remove(int $id)
	{
		//
	}
}