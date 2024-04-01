<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Packages;

class PackagesData
{
	public $packagesData = [];

	public function getAllData()
	{
		return ['packagesData' => $this->packagesData];
	}

	public function __set($key, $value)
	{
		$this->packagesData[$key] = $value;
	}

	public function __unset($key)
	{
		if (isset($this->packagesData[$key])) {
			unset($this->packagesData[$key]);
		}
	}

	public function __get($key)
	{
		if (isset($this->packagesData[$key])) {
			return $this->packagesData[$key];
		} else {
			throw new \Exception('PackagesData key "' . $key . '" does not exists!');
		}
	}

	public function __isset($key)
	{
		return array_key_exists($key, $this->packagesData);
	}
}