<?php

namespace System\Base\Providers\LoggerServiceProvider\Db;

use System\Base\BasePackage;
use System\Base\Providers\LoggerServiceProvider\Db\Model\Logs as LogsModel;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Logs extends BasePackage
{
	protected $modelToUse = LogsModel::class;

	protected $packageName = 'logs';

	public $logs;

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;

		${$this->packageName} = new $this->modelToUse();
	}
}