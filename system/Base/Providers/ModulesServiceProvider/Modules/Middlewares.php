<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Middlewares as MiddlewaresModel;

class Middlewares extends BasePackage
{
	protected $middlewares;

	public function getAllMiddlewares($conditions = null)
	{
		if (!$this->middlewares) {
			$this->middlewares = MiddlewaresModel::find($conditions, 'middlewares')->toArray();
		}

		return $this;
	}
}