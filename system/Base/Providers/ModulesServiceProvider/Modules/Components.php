<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Components as ComponentsModel;

class Components extends BasePackage
{
	protected $components;

	public function getAllComponents($conditions = null)
	{
		if (!$this->components) {
			$this->components = ComponentsModel::find($conditions, 'components')->toArray();
		}

		return $this;
	}
}