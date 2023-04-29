<?php

namespace Apps\Core\Packages\Module;

use Apps\Core\Packages\Module\Info;
use Apps\Core\Packages\Module\Install;
use Apps\Core\Packages\Module\Remove;
use Apps\Core\Packages\Module\TestEmail;
use Apps\Core\Packages\Module\Update;

use System\Base\BasePackage;

class Module extends BasePackage
{
	public function moduleInfo()
	{
		return new Info;
	}

	public function moduleSettings($module)
	{
		$module = 'Apps\\Core\\Packages\\Module\\Settings\\' . $module;

		return new $module;
	}

	public function installModule()
	{
		return new Install;
	}

	public function updateModule()
	{
		return new Update;
	}

	public function removeModule()
	{
		return new Remove;
	}

	public function testEmail()
	{
		return new TestEmail;
	}
}