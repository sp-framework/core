<?php

namespace Apps\Dash\Packages\Module;

use Apps\Dash\Packages\Module\Info;
use Apps\Dash\Packages\Module\Install;
use Apps\Dash\Packages\Module\Remove;
use Apps\Dash\Packages\Module\TestEmail;
use Apps\Dash\Packages\Module\Update;

use System\Base\BasePackage;

class Module extends BasePackage
{
	public function moduleInfo()
	{
		return new Info;
	}

	public function moduleSettings($module)
	{
		$module = 'Apps\\Dash\\Packages\\Module\\Settings\\' . $module;

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