<?php

namespace Applications\Admin\Components\Modules;

use Applications\Admin\Packages\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModuleComponent extends BaseComponent
{
	public function viewAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		$viewModule = $modules->viewModule($this->getData);

		$this->viewsData->module = $viewModule->packagesData['info'];

		return $this->generateView();
	}

	public function installAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

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

	public function updateAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

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