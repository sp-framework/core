<?php

namespace Applications\Ecom\Admin\Packages\Module;

use Applications\Ecom\Admin\Packages\Module\Info;
use Applications\Ecom\Admin\Packages\Module\Install;
use Applications\Ecom\Admin\Packages\Module\Remove;
use Applications\Ecom\Admin\Packages\Module\TestEmail;
use Applications\Ecom\Admin\Packages\Module\Update;

use System\Base\BasePackage;

class Module extends BasePackage
{
	public function moduleInfo()
	{
		return new Info;
	}

	public function moduleSettings($module)
	{
		$module = 'Applications\\Ecom\\Admin\\Packages\\Module\\Settings\\' . $module;

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