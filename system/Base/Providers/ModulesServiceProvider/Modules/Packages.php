<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use System\Base\BasePackage;
use System\Base\Interfaces\BasePackageInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Packages as PackagesModel;;
use System\Base\Providers\ModulesServiceProvider\Packages\PackagesData;

class Packages extends BasePackage implements BasePackageInterface
{
	private $model;

	public $packages;

	public function getAll($conditions = null)
	{
		if ($this->cacheKey) {
			$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
		}

		if (!$this->packages) {

			$this->model = PackagesModel::find($parameters);

			$this->packages = $this->model->toArray();
		}

		return $this;
	}

	public function add(array $data)
	{
		if ($data) {
			$package = new PackagesModel();

			return $package->add($data, $this->cacheKey);
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