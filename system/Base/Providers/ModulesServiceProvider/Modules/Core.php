<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Di\DiInterface;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Core as CoreModel;

class Core extends BasePackage
{
	protected $core;

	public function getCoreInfo()
	{
		return $this->core;
	}

	public function getAllCores()
	{
		if (!$this->core) {
			$this->core = CoreModel::find(null, 'core')->toArray();
		}

		return $this;
	}
}