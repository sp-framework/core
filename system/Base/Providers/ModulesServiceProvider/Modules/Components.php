<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Components as ComponentsModel;

class Components extends BasePackage implements BasePackageInterface
{
	private $model;

	public $components;

	protected $cacheKey;

	public function getAll($conditions = null)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		}

		if (!$this->components) {

			$this->model = ComponentsModel::find($parameters);

			$this->components = $this->model->toArray();
		}

		return $this;
	}

	public function add(array $data)
	{
		if ($data) {
			$component = new ComponentsModel();

			return $component->add($data, $this->cacheKey);
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