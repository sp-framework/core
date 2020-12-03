<?php

namespace Applications\Core\Admin\Packages\Module\Settings;

use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Core extends BasePackage
{
	public function get()
	{
		$this->packagesData->type = 'core';

		$this->packagesData->core =
			$this->modules->core->core[0];

		$this->packagesData->settings =
			Json::decode($this->modules->core->core[0]['settings'], true);

		$this->packagesData->responseCode = 0;

		return $this;
	}
}