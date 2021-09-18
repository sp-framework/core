<?php

namespace System\Base;

use Phalcon\Helper\Arr;
use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
	protected $app;

	protected static $modelRelations = [];

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
	}

	public function initialize()
	{
		//
	}

	public function init()
	{
		$this->apps = $this->getDi()->getShared('apps');

		$this->app = $this->apps->getAppInfo();

		$this->modules = $this->getDi()->getShared('modules');

		return $this;
	}

	public function getModelRelations()
	{
		return self::$modelRelations;
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getNamedPackageForApp(
				Arr::last(explode('\\', $packageClass)),
				$this->app['id']
			);
	}
}