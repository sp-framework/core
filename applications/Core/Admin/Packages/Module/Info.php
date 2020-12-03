<?php

namespace Applications\Core\Admin\Packages\Module;

use System\Base\BasePackage;

class Info extends BasePackage
{
	protected $getData;

	public function runProcess($getData)
	{
		$this->getData = $getData;

		if ($this->getData['type'] === 'core') {

			$info = $this->core->getCoreInfo()[0]->getAllArr();

			$this->packagesData->info = $info;

		} else {

			$info = $this->modules->{$this->getData['type']}->getById($this->getData['id']);

			$info['dependencies'] = json_decode($info['dependencies'], true);

			$this->packagesData->info = $info;
		}

		// } else if ($this->getData['type'] === 'applications') {


		// } else if ($this->getData['type'] === 'components') {

		// 	$info = $this->components->getById($this->getData['id'])->getAllArr();

		// 	$info['dependencies'] = unserialize($info['dependencies']);

		// 	$this->packagesData->info = $info;

		// } else if ($this->getData['type'] === 'packages') {

		// 	$info = $this->packages->getById($this->getData['id'])->getAllArr();

		// 	$info['dependencies'] = unserialize($info['dependencies']);

		// 	$this->packagesData->info = $info;

		// } else if ($this->getData['type'] === 'middlewares') {

		// 	$info = $this->middlewares->getById($this->getData['id'])->getAllArr();

		// 	$info['dependencies'] = unserialize($info['dependencies']);

		// 	$this->packagesData->info = $info;

		// } else if ($this->getData['type'] === 'views') {

		// 	$info = $this->views->getById($this->getData['id'])->getAllArr();

		// 	$info['dependencies'] = unserialize($info['dependencies']);

		// 	$this->packagesData->info = $info;
		// }

		return $this->packagesData;
	}
}