<?php

namespace Applications\Admin\Packages;

use Applications\Admin\Packages\Module\Info;
use Applications\Admin\Packages\Module\Install;
use Applications\Admin\Packages\Module\Remove;
use Applications\Admin\Packages\Module\Settings;
use Applications\Admin\Packages\Module\Update;
use System\Base\BasePackage;

class Module extends BasePackage
{
	public function moduleInfo()
	{
		return new Info;
	}

	public function moduleSettings()
	{
		return new Settings;
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
}