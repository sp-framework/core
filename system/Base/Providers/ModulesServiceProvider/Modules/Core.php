<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Di\DiInterface;
use System\Base\BasePackage;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Core as CoreModel;

class Core extends BasePackage implements BasePackageInterface
{
	private $model;

	public $core;

	protected $cacheKey;

	public function getCoreInfo()
	{
		return $this->core;
	}

	public function getAll(array $conditions = null)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		}

		if (!$this->core) {

			$this->model = CoreModel::find($parameters);

			$this->core = $this->model->toArray();
		}

		return $this;
	}

	public function add(array $data)
	{
		if ($data) {
			$core = new CoreModel();

			return $core->add($data, $this->cacheKey);
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