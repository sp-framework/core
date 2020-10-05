<?php

namespace Components\Admin\Modules;

use Packages\Admin\Modules;
use System\Base\BaseComponent;

class Module extends BaseComponent
{
	public function view()
	{
		$modules = $this->packages->use(Modules::class);

		$viewModule = $modules->viewModule($this->getData);

		$this->viewsData->module = $viewModule->packagesData['info'];

		return $this->generateView();
	}

	public function install()
	{
		$modules = $this->packages->use(Modules::class);

		$installModule = $modules->installModule($this->postData);

		if ($installModule->packagesData['responseCode'] === 0) {

			$this->viewsData->responseCode = 0;

			$this->viewsData->responseMessage =
				rtrim(ucfirst($this->postData['type'])) . ' ' . ucfirst($this->postData['name']) . ' Installed Successfully! ' .
				'<br>Backup was successfully taken at location .backups/' . $installModule->packagesData['backupFile'];

			$this->flash->now(
				'success',
				rtrim(ucfirst($this->postData['type'])) . ' ' .
					ucfirst($this->postData['name']) . ' Installed Successfully! ' .
					'<br>Backup was successfully taken at location .backups/' .
					$installModule->packagesData['backupFile']
			);

			return $this->generateView();

		} else if ($installModule->packagesData['responseCode'] === 1) {

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = $installModule->packagesData['responseMessage'];

			return $this->generateView();
		}
	}

	public function update()
	{
		$modules = $this->packages->use(Modules::class);

		$updateModule = $modules->installModule($this->postData);

		if ($updateModule->packagesData['responseCode'] === 0) {

			$this->viewsData->responseCode = 0;

			$this->viewsData->responseMessage =
				rtrim(ucfirst($this->postData['type'])) . ' ' . ucfirst($this->postData['name']) . ' Updated Successfully! ' .
				'<br>Backup was successfully taken at location .backups/' . $updateModule->packagesData['backupFile'];

			$this->flash->now(
				'success',
				rtrim(ucfirst($this->postData['type'])) . ' ' .
					ucfirst($this->postData['name']) . ' Updated Successfully! ' .
					'<br>Backup was successfully taken at location .backups/' .
					$updateModule->packagesData['backupFile']
			);

			return $this->generateView();

		} else if ($updateModule->packagesData['responseCode'] === 1) {

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = $updateModule->packagesData['responseMessage'];

			return $this->generateView();
		}
	}
}