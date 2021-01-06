<?php

namespace Applications\Dash\Packages\Module;

use Applications\Dash\Packages\Module\Info;
use Applications\Dash\Packages\Module\Install;
use Applications\Dash\Packages\Module\Remove;
use Applications\Dash\Packages\Module\TestEmail;
use Applications\Dash\Packages\Module\Update;

use System\Base\BasePackage;

class Module extends BasePackage
{
	public function moduleInfo()
	{
		return new Info;
	}

	public function moduleSettings($module)
	{
		$module = 'Applications\\Dash\\Packages\\Module\\Settings\\' . $module;

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