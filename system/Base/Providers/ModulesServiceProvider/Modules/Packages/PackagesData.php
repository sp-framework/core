<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Packages;

class PackagesData
{
	public $packagesData = [];

	public function __set($key, $value)
	{
		$this->packagesData[$key] = $value;
	}

	public function getAllData()
	{
		return ['packagesData' => $this->packagesData];
	}

	public function __get($key)
	{
		return $this->packagesData[$key];
	}
}