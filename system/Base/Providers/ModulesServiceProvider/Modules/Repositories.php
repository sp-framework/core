<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;

class Repositories extends BasePackage
{
	public $repositories;

	public function init()
	{
		$this->repositories = $this->basepackages->apiClientServices->getApiByCategory('repos');

		return $this;
	}
}